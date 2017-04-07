<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IndexColumnGetterChoiceType extends AbstractType
{
    /**
     * @var array
     */
    private $getters;

    /**
     * @param array $getters
     */
    public function __construct(array $getters)
    {
        $this->getters = $getters;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip($this->getters),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_index_getter_choice';
    }
}
