<?php

namespace CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class TimespanConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateFrom', NumberType::class) //TODO: Mabye DateType?
            ->add('dateTo', NumberType::class) //TODO: Mabye DateType?
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule_condition_timespan';
    }
}
