<?php

namespace CoreShop\Bundle\TaxationBundle\Form;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRule;
use CoreShop\Component\Taxation\Model\TaxRuleGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxRuleGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxRules',
                CollectionType::class, [
                'entry_type' => TaxRule::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_label' => 'sylius.form.country.add_province',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaxRuleGroup::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'coreshop_taxation_tax_rule_group';
    }
}
