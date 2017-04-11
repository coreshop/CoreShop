<?php

namespace CoreShop\Bundle\TaxationBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxRuleGroupChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $taxRuleGroupRepository;

    /**
     * @param RepositoryInterface $taxRuleGroupRepository
     */
    public function __construct(RepositoryInterface $taxRuleGroupRepository)
    {
        $this->taxRuleGroupRepository = $taxRuleGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $taxRuleGroups = $this->taxRuleGroupRepository->findAll();

                    /*
                     * PHP 5.* bug, fixed in PHP 7: https://bugs.php.net/bug.php?id=50688
                     * "usort(): Array was modified by the user comparison function"
                     */
                    @usort($taxRuleGroups, function ($a, $b) {
                        return $a->getName() < $b->getName() ? -1 : 1;
                    });

                    return $taxRuleGroups;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false
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
        return 'coreshop_tax_rule_group_choice';
    }
}
