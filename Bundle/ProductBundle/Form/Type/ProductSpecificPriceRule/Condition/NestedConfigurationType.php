<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRule\Condition;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleConditionCollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class NestedConfigurationType extends \CoreShop\Bundle\ProductBundle\Form\Type\Condition\NestedConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('conditions', ProductSpecificPriceRuleConditionCollectionType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_specific_price_rule_condition_products';
    }
}
