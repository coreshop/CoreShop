<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
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
    public function __construct(
        private RepositoryInterface $taxRuleGroupRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    /**
                     * @var TaxRuleGroupInterface[] $taxRuleGroups
                     */
                    $taxRuleGroups = $this->taxRuleGroupRepository->findAll();

                    usort($taxRuleGroups, function (TaxRuleGroupInterface $a, TaxRuleGroupInterface $b): int {
                        return $a->getName() <=> $b->getName();
                    });

                    return $taxRuleGroups;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_tax_rule_group_choice';
    }
}
