<?php

namespace CoreShop\Bundle\AddressBundle\Form\Type;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CountryType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('isoCode', TextType::class)
            ->add('active', CheckboxType::class)
            ->add('currency', CurrencyChoiceType::class)
            ->add('useStoreCurrency', CheckboxType::class)
            ->add('zone', ZoneChoiceType::class, [
                'label' => 'coreshop.form.address.zone',
                'active' => false
            ])
            ->add('addressFormat', TextareaType::class)
            ->add('stores', StoreChoiceType::class, [
                "multiple" => true
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_country';
    }
}
