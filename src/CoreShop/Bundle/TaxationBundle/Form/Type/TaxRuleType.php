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

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxRuleType extends AbstractResourceType
{
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
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_tax_rule';
    }
}
