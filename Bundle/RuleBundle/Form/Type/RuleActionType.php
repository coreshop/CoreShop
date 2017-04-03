<?php

namespace CoreShop\Bundle\RuleBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class RuleActionType extends AbstractConfigurableRuleElementType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', RuleActionChoiceType::class, [
                'label' => 'coreshop.form.rule_action.type',
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
        return 'coreshop_rule_action';
    }
}
