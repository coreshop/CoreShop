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
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroup;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartTaxProcessor implements CartProcessorInterface
{
    private $taxCollector;
    private $defaultAddressProvider;
    private $taxCalculationFactory;

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
    public function process(OrderInterface $cart): void
    {
        $cart->setTaxes(null);

        $usedTaxes = [];

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $usedTaxes = $this->taxCollector->mergeTaxes($item->getTaxes() instanceof Fieldcollection ? $item->getTaxes()->getItems() : [],
                $usedTaxes);
        }

        $usedTaxes = $this->collectionShippingTaxes($cart, $usedTaxes);

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($usedTaxes);
        $cart->setTaxes($fieldCollection);
    }

    private function collectionShippingTaxes(OrderInterface $cart, array $usedTaxes = []): array
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\OrderInterface) {
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

        $taxRule = $carrier->getTaxRule();

        if (!$taxRule instanceof TaxRuleGroup) {
            return $usedTaxes;
        }

        $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($taxRule, $address);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            $cart->setShippingTaxRate($taxCalculator->getTotalRate());
            $shipping = $cart->getShipping(false);

            return $this->taxCollector->mergeTaxes($this->taxCollector->collectTaxes($taxCalculator, $shipping),
                $usedTaxes);
        }

        return $usedTaxes;
    }
}
