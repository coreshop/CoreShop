<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

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
            ->add('type', IndexColumnChoiceType::class, [
                'label' => 'coreshop.form.index_column.type'
            ])
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
