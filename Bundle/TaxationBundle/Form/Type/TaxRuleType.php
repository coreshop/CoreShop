<?php

namespace CoreShop\Bundle\TaxationBundle\Form\Type;

use CoreShop\Bundle\AddressBundle\Form\Type\CountryChoiceType;
use CoreShop\Bundle\AddressBundle\Form\Type\StateChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxRuleType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', CountryChoiceType::class, [
                'label' => 'coreshop.form.address.country',
                'active' => true,
                'required' => false
            ])
            ->add('state', StateChoiceType::class, [
                'label' => 'coreshop.form.address.country',
                'active' => true,
                'required' => false
            ])
            ->add('taxRate', TaxRateChoiceType::class, [
                'label' => 'coreshop.form.taxation.tax_rule',
                'active' => true
            ])
            ->add('behavior', ChoiceType::class, [
                'choices' => [
                    [
                        'name' => 'coreshop.form.tax_rule.behaviour.disable',
                        'id' => 0
                    ],
                    [
                        'name' => 'coreshop.form.tax_rule.behaviour.combine',
                        'id' => 1
                    ],
                    [
                        'name' => 'coreshop.form.tax_rule.behaviour.one_after_another',
                        'id' => 3
                    ]
                ],
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'label' => 'coreshop.form.taxation.tax_rule',
                'placeholder' => 'coreshop.form.tax_rule.select',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'coreshop_tax_rule';
    }
}
