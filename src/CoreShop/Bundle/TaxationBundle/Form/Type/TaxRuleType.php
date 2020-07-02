<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\TaxationBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxRuleType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxRate', TaxRateChoiceType::class, [
                'active' => null,
            ])
            ->add('behavior', ChoiceType::class, [
                'choices' => [
                    'coreshop.form.tax_rule.behaviour.disable' => 0,
                    'coreshop.form.tax_rule.behaviour.combine' => 1,
                    'coreshop.form.tax_rule.behaviour.one_after_another' => 2,
                ],
                'choice_translation_domain' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_tax_rule';
    }
}
