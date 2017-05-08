<?php

namespace CoreShop\Bundle\ShippingBundle\Calculator;

use CoreShop\Bundle\ShippingBundle\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Bundle\ShippingBundle\Processor\ShippingRuleActionProcessorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;

class CarrierShippingRulePriceCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * @var CarrierShippingRuleCheckerInterface
     */
    protected $carrierShippingRuleChecker;

    /**
     * @var ShippingRuleActionProcessorInterface
     */
    protected $shippingRuleProcessor;

    /**
     * @param CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
     * @param ShippingRuleActionProcessorInterface $shippingRuleProcessor
     */
    public function __construct(
        CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker,
        ShippingRuleActionProcessorInterface $shippingRuleProcessor
    ) {
        $this->carrierShippingRuleChecker = $carrierShippingRuleChecker;
        $this->shippingRuleProcessor = $shippingRuleProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true)
    {
        /**
         * First valid price rule wins. so, we loop through all ShippingRuleGroups
         * get the first valid one, and process it for the price
         */
        $shippingRuleGroup = $this->carrierShippingRuleChecker->isShippingRuleValid($carrier, $cart, $address);

        if ($shippingRuleGroup instanceof ShippingRuleGroupInterface) {
            $price = $this->shippingRuleProcessor->getPrice($shippingRuleGroup->getShippingRule(), $carrier, $address, $withTax);
            $modifications = $this->shippingRuleProcessor->getModification($shippingRuleGroup->getShippingRule(), $carrier, $address, $price);

            return $price + $modifications;
        }

        return 0;
    }
}
