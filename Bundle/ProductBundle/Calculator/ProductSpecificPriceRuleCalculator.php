<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

class ProductSpecificPriceRuleCalculator extends AbstractPriceRuleCalculator
{
    /**
     * {@inheritdoc}
     */
    protected function getPriceRules($subject)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        /**
         * @var $subject ProductInterface
         */
        return $subject->getSpecificPriceRules();
    }
}
