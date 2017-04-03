<?php

namespace CoreShop\Component\Product\Rule\Checker;

final class QuantityConditionChecker implements ProductPriceRuleConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        return true;
    }
}