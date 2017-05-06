<?php

namespace CoreShop\Component\Shipping\Rule\Condition;

abstract class AbstractConditionChecker implements ShippingConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        if (!is_array($subject)) {
            throw new \InvalidArgumentException('Shipping Rule Condition $subject needs to be an array with values cart, address and carrier');
        }

        if (!array_key_exists('carrier', $subject) || !array_key_exists('cart', $subject) || !array_key_exists('address', $subject)) {
            throw new \InvalidArgumentException('Shipping Rule Condition $subject needs to be an array with values cart, address and carrier');
        }

        return $this->isShippingRuleValid($subject['carrier'], $subject['cart'], $subject['address'], $configuration);
    }

}
