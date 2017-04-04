<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type\Column;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class IndexColumnTypeObjectBrickType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
            ->add('className', IntegerType::class, [
                'label' => 'coreshop.form.index.column_type.object_brick.class_name',
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                    new Type(['type' => 'numeric', 'groups' => ['coreshop']]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_index_column_type_object_brick';
    }
}
