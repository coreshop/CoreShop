<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleConditionChoiceType;

class CartPriceRuleConditionChoiceType extends RuleConditionChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule_action_condition_choice';
    }
}
