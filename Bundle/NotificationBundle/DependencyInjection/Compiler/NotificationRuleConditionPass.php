<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection\Compiler;

final class NotificationRuleConditionPass extends AbstractNotificationRulePass
{
    protected function getType()
    {
        return 'conditions';
    }

    protected function getIdentifier()
    {
        return 'coreshop.notification_rule.conditions';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.notification_rule.condition';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.notification_rule.conditions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.notification_rule.conditions';
    }
}
