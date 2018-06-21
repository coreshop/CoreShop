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

namespace CoreShop\Bundle\CoreBundle;

final class Events
{
    /**
     * Fired before a order mail will be sent
     */
    const PRE_ORDER_MAIL_SEND = 'coreshop.order.mail.pre_send';

    /**
     * Fired after a order mail has been sent
     */
    const POST_ORDER_MAIL_SEND = 'coreshop.order.mail.post_send';
}