<?php

namespace CoreShop\Bundle\TaxationBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxRateType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => TaxRateTranslationType::class,
                'label' => 'coreshop.form.tax_rate.translations',
            ])
            ->add('rate', NumberType::class, [
                'label' => 'coreshop.form.tax_rate.rate',
            ])
            ->add('active', CheckboxType::class);
    }

    public function getBlockPrefix()
    {
        return 'coreshop_tax_rate';
    }
}
