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

namespace CoreShop\Bundle\NotificationBundle;

final class Events
{
    /**
     * Fired before a rule will be applied.
     */
    public const PRE_APPLY = 'coreshop.notification.pre_process_rules';

    /**
     * Fired after a rule has been applied.
     */
    public const POST_APPLY = 'coreshop.notification.post_process_rules';
}
