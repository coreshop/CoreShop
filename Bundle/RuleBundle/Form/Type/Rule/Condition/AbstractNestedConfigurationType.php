<?php

namespace CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

abstract class AbstractNestedConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('operator', ChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']])
                ],
                'choices' => array('and' => 'and', 'or' => 'or'),
            ])
        ;
    }
}
