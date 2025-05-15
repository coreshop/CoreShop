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

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection\Compiler;

final class NotificationRuleActionPass extends AbstractNotificationRulePass
{
    public const NOTIFICATION_ACTION_TAG = 'coreshop.notification_rule.action';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.notification_rule.actions',
            'coreshop.form_registry.notification_rule.actions',
            'coreshop.notification_rule.actions',
            self::NOTIFICATION_ACTION_TAG,
            'actions',
        );
    }
}
