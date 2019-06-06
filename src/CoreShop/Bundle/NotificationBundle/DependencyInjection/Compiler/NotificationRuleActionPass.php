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

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection\Compiler;

final class NotificationRuleActionPass extends AbstractNotificationRulePass
{
    public const NOTIFICATION_ACTION_TAG = 'coreshop.notification_rule.action';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.notification_rule.actions',
            'coreshop.form_registry.notification_rule.actions',
            'coreshop.notification_rule.actions',
            self::NOTIFICATION_ACTION_TAG,
            'actions'
        );
    }
}
