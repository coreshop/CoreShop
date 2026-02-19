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

final class OrderPaymentTransitions
{
    public const string IDENTIFIER = 'coreshop_order_payment';

    public const string TRANSITION_REQUEST_PAYMENT = 'request_payment';

    public const string TRANSITION_PARTIALLY_AUTHORIZE = 'partially_authorize';

    public const string TRANSITION_AUTHORIZE = 'authorize';

    public const string TRANSITION_PARTIALLY_PAY = 'partially_pay';

    public const string TRANSITION_CANCEL = 'cancel';

    public const string TRANSITION_PAY = 'pay';

    public const string TRANSITION_PARTIALLY_REFUND = 'partially_refund';

    public const string TRANSITION_REFUND = 'refund';
}
