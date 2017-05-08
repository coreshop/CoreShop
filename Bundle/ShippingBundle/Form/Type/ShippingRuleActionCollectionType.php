<?php

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleActionCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShippingRuleActionCollectionType extends RuleActionCollectionType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', ShippingRuleActionType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_shipping_action_collection';
    }
}
