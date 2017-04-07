<?php

namespace CoreShop\Bundle\ProductBundle\Rule\Condition;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class ProductsConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        return in_array($subject->getId(), $configuration['products']);
    }
}
