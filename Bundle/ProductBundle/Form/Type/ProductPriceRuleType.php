<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductPriceRuleType extends RuleType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextareaType::class, [
                'label' => 'coreshop.form.rule.name',
            ])
            ->add('conditions', ProductPriceRuleConditionCollectionType::class, [
                'label' => 'coreshop.form.rule.conditions',
            ])
            ->add('actions', ProductPriceRuleActionCollectionType::class, [
                'label' => 'coreshop.form.rule.actions',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_price_rule';
    }
}
