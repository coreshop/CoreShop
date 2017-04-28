<?php

namespace CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition\AbstractNestedConfigurationType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleConditionCollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class NestedConfigurationType extends AbstractNestedConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('conditions', ShippingRuleConditionCollectionType::class)
        ;
    }
}
