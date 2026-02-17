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

namespace CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\RuleBundle\Form\Type\TimestampDateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class TimespanConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateFrom', TimestampDateType::class, [
                'label' => 'coreshop_condition_timespan_dateFrom',
            ])
            ->add('dateTo', TimestampDateType::class, [
                'label' => 'coreshop_condition_timespan_dateTo',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_rule_condition_timespan';
    }
}
