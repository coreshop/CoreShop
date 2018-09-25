<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\CartItemInterface;
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
     * @param TaxCollectorInterface $taxCollector
     * @param AddressProviderInterface $defaultAddressProvider
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(
        TaxCollectorInterface $taxCollector,
        AddressProviderInterface $defaultAddressProvider,
        TaxCalculatorFactoryInterface $taxCalculatorFactory
    )
    {
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
         * @var $item CartItemInterface
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
     * @param array $usedTaxes
     * @return array
     */
    protected function collectionShippingTaxes(CartInterface $cart, array $usedTaxes = [])
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

        if ($carrier instanceof Carrier && $carrier->getTaxRule() instanceof TaxRuleGroup) {
            $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($cart->getCarrier()->getTaxRule(), $address);

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $cart->setShippingTaxRate($taxCalculator->getTotalRate());
                $shipping = $cart->getShipping(false);

                return $this->taxCollector->mergeTaxes($this->taxCollector->collectTaxes($taxCalculator, $shipping), $usedTaxes);
            }
        }

        return $usedTaxes;
    }
}