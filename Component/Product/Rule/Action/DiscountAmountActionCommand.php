<?php

namespace CoreShop\Component\Product\Rule\Action;

use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

class DiscountAmountActionCommand implements ProductActionCommandInterface
{
    public function execute(RuleSubjectInterface $subject, array $configuration, RuleInterface $rule)
    {
        //Nothing to do here
        return true;
    }

    public function revert(RuleSubjectInterface $subject, array $configuration, RuleInterface $rule)
    {
        //Nothing to do here
        return true;
    }

    public function getDiscount(RuleSubjectInterface $subject, array $configuration, $withTax = true)
    {
        return $configuration['discount'] * 120; //$subject->getSubtotal
    }
}