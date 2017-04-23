<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CartPriceRuleType extends RuleType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('highlight', CheckboxType::class)
            ->add('active', CheckboxType::class)
            ->add('description', TextareaType::class)
            ->add('conditions', CartPriceRuleConditionCollectionType::class)
            ->add('actions', CartPriceRuleActionCollectionType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule';
    }
}
