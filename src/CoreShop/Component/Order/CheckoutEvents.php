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

class CheckoutEvents
{
    public const string CHECKOUT_STEP_PRE = 'coreshop.checkout.step.pre';

    public const string CHECKOUT_STEP_POST = 'coreshop.checkout.step.post';

    public const string CHECKOUT_DO_PRE = 'coreshop.checkout.do.pre';

    public const string CHECKOUT_DO_POST = 'coreshop.checkout.do.post';

    public const string CHECKOUT_PAYMENT_PRE = 'coreshop.checkout.payment.pre';
}
