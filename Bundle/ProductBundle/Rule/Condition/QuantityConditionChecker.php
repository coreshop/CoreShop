<?php

namespace CoreShop\Bundle\ProductBundle\Rule\Condition;

use CoreShop\Component\Core\Assert\Assert;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;

class QuantityConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        //TODO: Get Cart somehow! Maybe get Cart-Manager via DI?

        return true;
    }
}
