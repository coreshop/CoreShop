<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Repository\ProductPriceRuleRepositoryInterface;
use Webmozart\Assert\Assert;

class ProductPriceRuleCalculator extends AbstractPriceRuleCalculator
{
    /**
     * {@inheritdoc}
     */
    protected function getPriceRules($subject)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);
        Assert::isInstanceOf($this->productPriceRuleRepository, ProductPriceRuleRepositoryInterface::class);

        /**
         * @var $productPriceRuleRepository ProductPriceRuleRepositoryInterface
         */
        $productPriceRuleRepository = $this->productPriceRuleRepository;

        return $productPriceRuleRepository->findActive();
    }
}
