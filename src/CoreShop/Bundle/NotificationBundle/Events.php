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

namespace CoreShop\Bundle\NotificationBundle;

final class Events
{
    /**
     * Fired before a rule will be applied.
     */
    const PRE_APPLY = 'coreshop.notification.pre_process_rules';

    /**
     * Fired after a rule has been applied.
     */
    const POST_APPLY = 'coreshop.notification.post_process_rules';
}
