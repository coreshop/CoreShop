<?php

namespace CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleConditionCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('operator', TextType::class, [ //TODO: Change to ChoiceType with and && or
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']])
                ],
            ])
        ;
    }
}
