<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type\Getter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ClassificationStoreGetterFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupConfigId', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                ],
            ])
            ->add('keyConfigId', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_index_getter_classification_store';
    }
}
