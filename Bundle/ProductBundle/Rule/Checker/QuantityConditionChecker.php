<?php

namespace CoreShop\Bundle\ProductBundle\Rule\Checker;

use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;

class QuantityConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        return true;
    }
}