<?php

namespace CoreShop\Bundle\TaxationBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxRuleType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxRate', TaxRateChoiceType::class, [
                'active' => null,
            ])
            ->add('behavior', ChoiceType::class, [
                'choices' => [
                    'coreshop.form.tax_rule.behaviour.disable' => 0,
                    'coreshop.form.tax_rule.behaviour.combine' => 1,
                    'coreshop.form.tax_rule.behaviour.one_after_another' => 2,
                ],
                'choice_translation_domain' => false
            ]);
    }

    public function getBlockPrefix()
    {
        return 'coreshop_tax_rule';
    }
}
