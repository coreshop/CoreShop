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

namespace CoreShop\Component\Order;

final class OrderPaymentStates
{
    public const STATE_NEW = 'new';

    public const STATE_PARTIALLY_AUTHORIZED = 'partially_authorized';

    public const STATE_AUTHORIZED = 'authorized';

    public const STATE_AWAITING_PAYMENT = 'awaiting_payment';

    public const STATE_PARTIALLY_PAID = 'partially_paid';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_PAID = 'paid';

    public const STATE_PARTIALLY_REFUNDED = 'partially_refunded';

    public const STATE_REFUNDED = 'refunded';
}
