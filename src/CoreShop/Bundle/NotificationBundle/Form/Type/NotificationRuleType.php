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

namespace CoreShop\Bundle\NotificationBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class NotificationRuleType extends RuleType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextareaType::class, [
                'priority' => 100,
            ])
            ->add('active', CheckboxType::class, [
                'priority' => 90,
            ])
            ->add('type', NotificationRuleTypeChoiceType::class, [
                'priority' => 80,
            ])
            ->add('conditions', NotificationRuleConditionCollectionType::class)
            ->add('actions', NotificationRuleActionCollectionType::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_notification_rule';
    }
}
