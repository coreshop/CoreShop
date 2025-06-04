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

final class OrderInvoiceStates
{
    public const string STATE_NEW = 'new';

    public const string STATE_READY = 'ready';

    public const string STATE_CANCELLED = 'cancelled';

    public const string STATE_PARTIALLY_INVOICED = 'partially_invoiced';

    public const string STATE_INVOICED = 'invoiced';
}
