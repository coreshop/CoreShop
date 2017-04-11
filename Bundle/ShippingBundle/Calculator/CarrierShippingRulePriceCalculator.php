<?php

namespace CoreShop\Bundle\ShippingBundle\Calculator;

use CoreShop\Bundle\ShippingBundle\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Bundle\ShippingBundle\Rule\Condition\ShippingConditionCheckerInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;

class CarrierShippingRulePriceCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * @var CarrierShippingRuleCheckerInterface
     */
    protected $carrierShippingRuleChecker;

    /**
     * @var ServiceRegistryInterface
     */
    protected $actionServiceRegistry;

    /**
     * @param CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
     * @param ServiceRegistryInterface $actionServiceRegistry
     */
    public function __construct(
        CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker,
        ServiceRegistryInterface $actionServiceRegistry
    ) {
        $this->carrierShippingRuleChecker = $carrierShippingRuleChecker;
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address)
    {
        /**
         * First valid price rule wins. so, we loop through all ShippingRuleGroups
         * get the first valid one, and process it for the price
         */
        $shippingRuleGroup = $this->carrierShippingRuleChecker->isShippingRuleValid($carrier, $cart, $address);

        if ($shippingRuleGroup instanceof ShippingRuleGroupInterface) {
            $price = 0;
            $shippingRule = $shippingRuleGroup->getShippingRule();

            foreach ($shippingRule->getActions() as $action) {
                $processor = $this->actionServiceRegistry->get($action->getType());

                if ($processor instanceof ShippingConditionCheckerInterface) {
                    $price += $processor->getPrice($carrier, $carrier, $address, $action->getConfiguration());
                }
            }

            return $price;
        }

        return 0;
    }
}
