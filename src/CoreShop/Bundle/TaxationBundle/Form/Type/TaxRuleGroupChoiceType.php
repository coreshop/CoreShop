<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\TaxationBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
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

                    usort($taxRuleGroups, function (TaxRuleGroupInterface $a, TaxRuleGroupInterface $b): int {
                        return $a->getName() <=> $b->getName();
                    });

                    return $taxRuleGroups;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
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
        return 'coreshop_tax_rule_group_choice';
    }
}
