<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleActionChoiceType;

class CartPriceRuleActionChoiceType extends RuleActionChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule_action_choice';
    }
}
