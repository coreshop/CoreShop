<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleActionType;
use Symfony\Component\Form\FormBuilderInterface;

final class CartPriceRuleActionType extends RuleActionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', CartPriceRuleActionChoiceType::class, [
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule_action';
    }
}
