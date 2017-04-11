<?php

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShippingRuleChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $shippingRuleRepository;

    /**
     * @param RepositoryInterface $shippingRuleRepository
     */
    public function __construct(RepositoryInterface $shippingRuleRepository)
    {
        $this->shippingRuleRepository = $shippingRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $shippingRules = $this->shippingRuleRepository->findAll();

                    /*
                     * PHP 5.* bug, fixed in PHP 7: https://bugs.php.net/bug.php?id=50688
                     * "usort(): Array was modified by the user comparison function"
                     */
                    @usort($shippingRules, function ($a, $b) {
                        return $a->getName() < $b->getName() ? -1 : 1;
                    });

                    return $shippingRules;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'active' => true
            ])
        ;
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
        return 'coreshop_shipping_rule_choice';
    }
}
