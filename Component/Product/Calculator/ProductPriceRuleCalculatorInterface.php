<?php

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Rule\Model\RuleSubjectInterface;

interface ProductPriceRuleCalculatorInterface
{
    /**
     * @param RuleSubjectInterface $subject
     * @return mixed
     */
    public function getPrice(RuleSubjectInterface $subject);

    /**
     * @param RuleSubjectInterface $subject
     * @param $price
     * @param bool $withTax
     * @return mixed
     */
    public function getDiscount(RuleSubjectInterface $subject, $price, $withTax = true);
}