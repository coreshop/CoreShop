<?php

namespace CoreShop\Bundle\RuleBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class RuleConditionType extends AbstractConfigurableRuleElementType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_rule_condition';
    }
}
