<?php

namespace CoreShop\Component\Product\Rule\Action;

use CoreShop\Component\Promotion\Action\ActionCommandInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

interface ProductActionCommandInterface extends ActionCommandInterface
{
    /**
     * @param RuleSubjectInterface $subject
     * @param array $configuration
     * @param bool $withTax
     * @return mixed
     */
    public function getDiscount(RuleSubjectInterface $subject, array $configuration, $withTax = true);
}