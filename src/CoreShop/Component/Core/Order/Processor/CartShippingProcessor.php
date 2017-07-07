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

use CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\TaxRuleGroup;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\Object\Fieldcollection;

final class CartShippingProcessor implements CartProcessorInterface
{
    /**
     * @var CarrierPriceCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculationFactory;

    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     * @param TaxCalculatorFactoryInterface $taxCalculationFactory
     * @param TaxCollectorInterface $taxCollector
     */
    public function __construct(
        CarrierPriceCalculatorInterface $carrierPriceCalculator,
        TaxCalculatorFactoryInterface $taxCalculationFactory,
        TaxCollectorInterface $taxCollector
    )
    {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->taxCalculationFactory = $taxCalculationFactory;
        $this->taxCollector = $taxCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        if ($cart instanceof \CoreShop\Component\Core\Model\CartInterface) {
            //TODO: Should we add something like a default carrier determination?
            if (null === $cart->getCarrier()) {
                return;
            }

            $priceWithTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $cart->getShippingAddress(), true);
            $priceWithoutTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $cart->getShippingAddress(), false);

            $cart->setShipping($priceWithTax, true);
            $cart->setShipping($priceWithoutTax, false);

            if ($cart->getCarrier() instanceof Carrier && $cart->getCarrier()->getTaxRule() instanceof TaxRuleGroup) {
                $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($cart->getCarrier()->getTaxRule(), $cart->getShippingAddress());

                if ($taxCalculator instanceof TaxCalculatorInterface) {
                    $cart->setShippingTaxRate($taxCalculator->getTotalRate());

                    $usedTaxes = $cart->getTaxes() instanceof Fieldcollection ? $cart->getTaxes()->getItems() : [];
                    $usedTaxes = $this->taxCollector->mergeTaxes($this->taxCollector->collectTaxes($taxCalculator, $cart->getShipping(false)), $usedTaxes);

                    $fieldCollection = new Fieldcollection();
                    $fieldCollection->setItems($usedTaxes);

                    $cart->setTaxes($fieldCollection);
                }
            }
        }
    }
}