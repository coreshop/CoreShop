<?php

namespace CoreShop\Bundle\PromotionBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RuleType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'coreshop.form.rule.name',
            ])
            ->add('conditions', CollectionType::class, [
                'label' => 'coreshop.form.rule.conditions'
            ])
            ->add('actions', CollectionType::class, [
                'label' => 'coreshop.form.rule.actions'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_rule';
    }
}
