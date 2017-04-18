<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

class WeightConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $minWeight = $configuration['minWeight'];
        $maxWeight = $configuration['maxWeight'];
        $totalWeight = $cart->getTotalWeight();

        if ($minWeight > 0) {
            if ($totalWeight <= $minWeight) {
                return false;
            }
        }

        if ($maxWeight > 0) {
            if ($totalWeight >= $maxWeight) {
                return false;
            }
        }

        return true;
    }
}
