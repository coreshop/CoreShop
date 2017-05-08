<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleActionCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CartPriceRuleActionCollectionType extends RuleActionCollectionType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', CartPriceRuleActionType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule_action_collection';
    }
}
