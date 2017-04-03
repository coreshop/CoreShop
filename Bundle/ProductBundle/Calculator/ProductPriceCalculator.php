<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

/**
 * TODO: Add Checker as Interface and check rules
 */
class ProductPriceCalculator implements ProductPriceCalculatorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $productPriceRuleRepository;

    /**
     * @param RepositoryInterface $productPriceRuleRepository
     */
    public function __construct(RepositoryInterface $productPriceRuleRepository)
    {
        $this->productPriceRuleRepository = $productPriceRuleRepository;
    }

    public function getPrice($subject)
    {
        $rules = $this->productPriceRuleRepository->findAll();

        foreach ($rules as $rule) {

        }
    }

    public function getDiscount($subject, $price, $withTax = true)
    {
        // TODO: Implement getDiscount() method.
    }

}