<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
                'label' => 'coreshop_tax_rate',
                'active' => null,
            ])
            ->add('behavior', ChoiceType::class, [
                'label' => 'coreshop_tax_rule_behavior',
                'choices' => [
                    'coreshop_tax_rule_behavior_disable' => 0,
                    'coreshop_tax_rule_behavior_combine' => 1,
                    'coreshop_tax_rule_behavior_on_after_another' => 2,
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_tax_rule';
    }
}
