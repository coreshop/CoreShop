<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order;

final class Notes
{
    /**
     * Note Identifier for Payment.
     */
    const NOTE_PAYMENT = 'payment';

    /**
     * Note Identifier for Order History State Logging.
     */
    const ORDER_HISTORY_STATE_LOG = 'order_state_change';

    /**
     * Note Identifier for Update Order.
     */
    const NOTE_UPDATE_ORDER = 'update_order';

    /**
     * Note Identifier for Update Order Item.
     */
    const NOTE_UPDATE_ORDER_ITEM = 'update_order_item';

    /**
     * Note Identifier for emails.
     */
    const NOTE_EMAIL = 'email';

    /**
     * Note Identifier for order comments.
     */
    const NOTE_ORDER_COMMENT = 'order_comment';
}
