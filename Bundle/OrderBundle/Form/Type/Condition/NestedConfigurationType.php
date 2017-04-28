<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleConditionCollectionType;
use CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition\AbstractNestedConfigurationType;
use Symfony\Component\Form\FormBuilderInterface;

final class NestedConfigurationType extends AbstractNestedConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('conditions', CartPriceRuleConditionCollectionType::class)
        ;
    }
}
