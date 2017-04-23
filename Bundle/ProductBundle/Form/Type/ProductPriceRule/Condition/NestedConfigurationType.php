<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRule\Condition;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRuleConditionCollectionType;
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
            ->add('conditions', ProductPriceRuleConditionCollectionType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_price_rule_condition_products';
    }
}
