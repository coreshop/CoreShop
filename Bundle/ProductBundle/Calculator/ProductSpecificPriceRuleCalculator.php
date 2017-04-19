<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

class ProductSpecificPriceRuleCalculator extends AbstractPriceRuleCalculator
{
    /**
     * {@inheritdoc}
     */
    protected function getPriceRules($subject)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);
        Assert::isInstanceOf($this->productPriceRuleRepository, ProductSpecificPriceRuleRepositoryInterface::class);

        /**
         * @var $productPriceRuleRepository ProductSpecificPriceRuleRepositoryInterface
         */
        $productPriceRuleRepository = $this->productPriceRuleRepository;

        return $productPriceRuleRepository->findForProduct($subject);
    }
}
