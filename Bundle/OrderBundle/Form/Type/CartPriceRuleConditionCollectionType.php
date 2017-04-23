<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleConditionCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartPriceRuleConditionCollectionType extends RuleConditionCollectionType
{
public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', CartPriceRuleConditionType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule_condition_collection';
    }
}