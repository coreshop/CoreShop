<?php

namespace CoreShop\Bundle\CurrencyBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                'scale' => 5,
                'rounding_mode' => $options['rounding_mode'],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var ExchangeRateInterface $exchangeRate */
            $exchangeRate = $event->getData();
            $form = $event->getForm();

            $disabled = null !== $exchangeRate->getId();

            $form
                ->add('fromCurrency', CurrencyChoiceType::class, [
                    'required' => true,
                    'empty_data' => false,
                    'disabled' => $disabled,
                ])
                ->add('toCurrency', CurrencyChoiceType::class, [
                    'required' => true,
                    'empty_data' => false,
                    'disabled' => $disabled,
                ])
            ;
        });
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
