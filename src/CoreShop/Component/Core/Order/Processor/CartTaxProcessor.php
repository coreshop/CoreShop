<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributor;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroup;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartTaxProcessor implements CartProcessorInterface
{
    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @var AddressProviderInterface
     */
    private $defaultAddressProvider;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculationFactory;

    /**
     * @param TaxCollectorInterface         $taxCollector
     * @param AddressProviderInterface      $defaultAddressProvider
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(
        TaxCollectorInterface $taxCollector,
        AddressProviderInterface $defaultAddressProvider,
        TaxCalculatorFactoryInterface $taxCalculatorFactory
    ) {
        $this->taxCollector = $taxCollector;
        $this->defaultAddressProvider = $defaultAddressProvider;
        $this->taxCalculationFactory = $taxCalculatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $cart->setTaxes(null);

        $usedTaxes = [];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $usedTaxes = $this->taxCollector->mergeTaxes($item->getTaxes() instanceof Fieldcollection ? $item->getTaxes()->getItems() : [], $usedTaxes);
        }

        $usedTaxes = $this->collectionShippingTaxes($cart, $usedTaxes);

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($usedTaxes);
        $cart->setTaxes($fieldCollection);
    }

    /**
     * @param CartInterface $cart
     * @param array         $usedTaxes
     *
     * @return array
     */
    private function collectionShippingTaxes(CartInterface $cart, array $usedTaxes = [])
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\CartInterface) {
            return $usedTaxes;
        }

        if (null === $cart->getCarrier()) {
            return $usedTaxes;
        }

        $address = $cart->getShippingAddress();

        if (null === $address) {
            $address = $this->defaultAddressProvider->getAddress($cart);
        }

        if (null === $address) {
            return $usedTaxes;
        }

        $carrier = $cart->getCarrier();

        if (!$carrier instanceof Carrier) {
            return $usedTaxes;
        }

        $useCartItemForTaxCalculation = $carrier->getCartBasedTaxation();
        if ($useCartItemForTaxCalculation) {
            return $this->calculateByCartItems($cart, $usedTaxes, $address);
        }

        return $this->calculateByTaxRule($cart, $usedTaxes, $carrier, $address);
    }

    /**
     * @param CartInterface $cart
     * @param array $usedTaxes
     * @param $address
     * @return array|\CoreShop\Component\Taxation\Model\TaxItemInterface[]
     */
    private function calculateByCartItems(
        CartInterface $cart,
        array $usedTaxes,
        $address
    ) {
        $totalAmount = [];
        $taxRuleGroup = [];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if ($item->getDigitalProduct() === true) {
                continue;
            }

            if (!$item->getProduct()->getTaxRule()) {
                continue;
            }

            $taxRule = $item->getProduct()->getTaxRule();

            if (!in_array($taxRule->getId(), $totalAmount)) {
                $totalAmount[$taxRule->getId()] = 0;
            }

            $totalAmount[$taxRule->getId()] += $item->getTotal(true);
            $taxRuleGroup[] = $taxRule;
        }

        if (\count($taxRuleGroup) === 0) {
            return $usedTaxes;
        }

        $shippingAdjustments = $cart->getAdjustments(AdjustmentInterface::SHIPPING);
        if (!$shippingAdjustments) {
            return $usedTaxes;
        }

        /** @var Fieldcollection\Data\CoreShopAdjustment $shippingAdjustment */
        $shippingAdjustment = $shippingAdjustments[0];
        $shippingPrice = $shippingAdjustment->getPimcoreAmountGross();
        $distributor = new ProportionalIntegerDistributor();
        $distributedAmount = $distributor->distribute($totalAmount, $shippingPrice);

        $shippingTaxAmount = 0;
        foreach ($distributedAmount as $i => $amount) {
            $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($taxRuleGroup[$i], $address);

            if (!$taxCalculator) {
                continue;
            }

            $shippingTax = $this->taxCollector->collectTaxesFromGross($taxCalculator, $amount);
            $usedTaxes = $this->taxCollector->mergeTaxes($shippingTax, $usedTaxes);
            $shippingTaxAmount += \array_shift($shippingTax)->getAmount();
        }

        $cart->removeAdjustments(AdjustmentInterface::SHIPPING);
        $shippingAdjustment->setPimcoreAmountNet($shippingAdjustment->getPimcoreAmountGross() - $shippingTaxAmount);
        $cart->addAdjustment($shippingAdjustment);

        return $usedTaxes;
    }

    /**
     * @param CartInterface $cart
     * @param array $usedTaxes
     * @param $carrier
     * @param $address
     * @return array|\CoreShop\Component\Taxation\Model\TaxItemInterface[]
     */
    private function calculateByTaxRule(
        CartInterface $cart,
        array $usedTaxes,
        $carrier,
        $address
    ) {
        $taxRule = $carrier->getTaxRule();

        if (!$taxRule instanceof TaxRuleGroup) {
            return $usedTaxes;
        }

        $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($taxRule, $address);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            $cart->setShippingTaxRate($taxCalculator->getTotalRate());
            $shipping = $cart->getShipping(false);

            return $this->taxCollector->mergeTaxes($this->taxCollector->collectTaxes($taxCalculator, $shipping), $usedTaxes);
        }

        return $usedTaxes;
    }
}
