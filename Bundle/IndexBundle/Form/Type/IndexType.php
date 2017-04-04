<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class IndexType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'coreshop.form.index.name',
            ])
            ->add('type', TextType::class, [ //TODO: Make this configurable using tags
                'label' => 'coreshop.form.index.columns',
            ])
            ->add('class', TextType::class, [ //TODO: Make this configurable using tags
                'label' => 'coreshop.form.index.class',
            ])
            ->add('columns', IndexColumnCollectionType::class, [
                'label' => 'coreshop.form.index.columns'
            ])
            ->add('config', TextType::class, [ //TODO: needs to be configurable!!, depends on type
                'label' => 'coreshop.form.index.config'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_index';
    }
}
