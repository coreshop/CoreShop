<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceRuleCalculatorInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

/**
 * TODO: Add Checker as Interface and check rules
 */
class ProductPriceRuleCalculator implements ProductPriceRuleCalculatorInterface
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

    public function getPrice(RuleSubjectInterface $subject)
    {
        $rules = $this->productPriceRuleRepository->findAll();

        foreach ($rules as $rule) {

        }
    }

    public function getDiscount(RuleSubjectInterface $subject, $price, $withTax = true)
    {
        // TODO: Implement getDiscount() method.
    }

}