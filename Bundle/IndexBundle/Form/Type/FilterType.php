<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('orderKey', TextType::class)
            ->add('orderDirection', TextType::class) //Make ChoiceType with ASC and DESC
            ->add('preConditions', FilterConditionCollectionType::class)
            ->add('conditions', FilterConditionCollectionType::class)
            ->add('resultsPerPage', NumberType::class)
            ->add('index', IndexChoiceType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_filter';
    }
}
