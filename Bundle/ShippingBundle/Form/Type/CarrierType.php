<?php

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CarrierType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('label', TextType::class)
            ->add('trackingUrl', TextType::class)
            ->add('isFree', CheckboxType::class)
            ->add('rangeBehaviour', ChoiceType::class, [
                'choices'  => [
                    'deactivate' => CarrierInterface::RANGE_BEHAVIOUR_DEACTIVATE,
                    'largest' => CarrierInterface::RANGE_BEHAVIOUR_LARGEST
                ]
            ])
            ->add('shippingRules', ShippingRuleGroupCollectionType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_carrier';
    }
}
