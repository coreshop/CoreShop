<?php

namespace CoreShop\Bundle\CurrencyBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


final class ExchangeRateType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exchangeRate', NumberType::class, [
                'required' => true,
                'scale' => 10,
                'rounding_mode' => $options['rounding_mode'],
            ])
            ->add('fromCurrency', CurrencyChoiceType::class, [
                'required' => true,
                'empty_data' => false
            ])
            ->add('toCurrency', CurrencyChoiceType::class, [
                'required' => true,
                'empty_data' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('rounding_mode', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_exchange_rate';
    }
}
