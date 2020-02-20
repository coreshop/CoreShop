<?php
declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingTaxStrategyChoiceType extends AbstractType
{
    /**
     * @var array
     */
    private $stategies;

    /**
     * @param array $stategies
     */
    public function __construct(array $stategies)
    {
        $this->stategies = $stategies;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip($this->stategies),
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
        return 'coreshop_shipping_tax_strategy';
    }
}
