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

final class OrderPaymentStates
{
    public const string STATE_NEW = 'new';

    public const string STATE_PARTIALLY_AUTHORIZED = 'partially_authorized';

    public const string STATE_AUTHORIZED = 'authorized';

    public const string STATE_AWAITING_PAYMENT = 'awaiting_payment';

    public const string STATE_PARTIALLY_PAID = 'partially_paid';

    public const string STATE_CANCELLED = 'cancelled';

    public const string STATE_PAID = 'paid';

    public const string STATE_PARTIALLY_REFUNDED = 'partially_refunded';

    public const string STATE_REFUNDED = 'refunded';
}
