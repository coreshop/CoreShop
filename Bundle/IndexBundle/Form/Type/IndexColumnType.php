<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class IndexColumnType extends AbstractConfigurableIndexColumnElementType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', IndexColumnChoiceType::class)
            ->add('objectType', TextType::class)
            ->add('name', TextType::class)
            ->add('objectKey', TextType::class)
            ->add('columnType', TextType::class)
            ->add('getter', TextType::class)
            ->add('getterConfig', TextType::class)
            ->add('interpreter', TextType::class)
            ->add('interpreterConfig', TextType::class)
            ->add('dataType', TextType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_index_column';
    }
}
