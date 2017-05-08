<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleActionCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductSpecificPriceRuleActionCollectionType extends RuleActionCollectionType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', ProductSpecificPriceRuleActionType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_specific_price_rule_action_collection';
    }
}
