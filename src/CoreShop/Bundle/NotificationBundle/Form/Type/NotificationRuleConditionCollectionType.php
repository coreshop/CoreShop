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

namespace CoreShop\Bundle\NotificationBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleConditionCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationRuleConditionCollectionType extends RuleConditionCollectionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', NotificationRuleConditionType::class);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_notification_rule_condition_collection';
    }
}
