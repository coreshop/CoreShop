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
