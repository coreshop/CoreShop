<?php

namespace CoreShop\Bundle\ShippingBundle\Form\Type\Action;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdditionPercentActionConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('percent', NumberType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']])
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_shipping_rule_action_addition_percent';
    }
}