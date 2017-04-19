<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleActionChoiceType;

class ProductSpecificPriceRuleActionChoiceType extends RuleActionChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_specific_price_rule_action_choice';
    }
}
