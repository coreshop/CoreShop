<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductPriceRuleType extends RuleType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', CheckboxType::class, [
                'label' => 'coreshop.form.rule.name',
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
