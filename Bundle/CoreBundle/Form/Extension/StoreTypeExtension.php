<?php

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class StoreTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('baseCurrency', CurrencyChoiceType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return StoreType::class;
    }
}
