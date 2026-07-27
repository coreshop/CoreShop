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

namespace CoreShop\Component\Order;

final class Notes
{
    /**
     * Note Identifier for Payment.
     */
    public const string NOTE_PAYMENT = 'payment';

    /**
     * Note Identifier for Order History State Logging.
     */
    public const string ORDER_HISTORY_STATE_LOG = 'order_state_change';

    /**
     * Note Identifier for Update Order.
     */
    public const string NOTE_UPDATE_ORDER = 'update_order';

    /**
     * Note Identifier for Update Order Item.
     */
    public const string NOTE_UPDATE_ORDER_ITEM = 'update_order_item';

    /**
     * Note Identifier for emails.
     */
    public const string NOTE_EMAIL = 'email';

    /**
     * Note Identifier for order comments.
     */
    public const string NOTE_ORDER_COMMENT = 'order_comment';

    /**
     * Note Identifier for backend order updates.
     */
    public const string NOTE_ORDER_BACKEND_UPDATE_SAVE = 'order_backend_update_save';
}
