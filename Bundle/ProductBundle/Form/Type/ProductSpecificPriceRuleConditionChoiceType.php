<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleConditionChoiceType;

class ProductSpecificPriceRuleConditionChoiceType extends RuleConditionChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_specific_price_rule_action_condition_choice';
    }
}
