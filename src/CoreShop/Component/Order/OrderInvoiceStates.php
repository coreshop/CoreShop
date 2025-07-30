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

final class OrderInvoiceStates
{
    public const string STATE_NEW = 'new';

    public const string STATE_READY = 'ready';

    public const string STATE_CANCELLED = 'cancelled';

    public const string STATE_PARTIALLY_INVOICED = 'partially_invoiced';

    public const string STATE_INVOICED = 'invoiced';
}
