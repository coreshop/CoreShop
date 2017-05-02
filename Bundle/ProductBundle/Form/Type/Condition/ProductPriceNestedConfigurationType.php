<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRuleConditionCollectionType;
use CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition\AbstractNestedConfigurationType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductPriceNestedConfigurationType extends AbstractNestedConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('conditions', ProductPriceRuleConditionCollectionType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_rule_condition_nested';
    }
}
