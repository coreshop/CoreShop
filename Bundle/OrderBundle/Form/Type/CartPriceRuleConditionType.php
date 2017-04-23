<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleConditionType;
use Symfony\Component\Form\FormBuilderInterface;

final class CartPriceRuleConditionType extends RuleConditionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', CartPriceRuleConditionChoiceType::class, [
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
        return 'coreshop_cart_price_rule_condition';
    }
}
