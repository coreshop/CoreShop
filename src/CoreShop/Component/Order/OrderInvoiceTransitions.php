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

final class OrderInvoiceTransitions
{
    public const IDENTIFIER = 'coreshop_order_invoice';

    public const TRANSITION_REQUEST_INVOICE = 'request_invoice';

    public const TRANSITION_PARTIALLY_INVOICE = 'partially_invoice';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_INVOICE = 'invoice';
}
