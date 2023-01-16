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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order;

class CheckoutEvents
{
    public const CHECKOUT_STEP_PRE = 'coreshop.checkout.step.pre';

    public const CHECKOUT_STEP_POST = 'coreshop.checkout.step.post';

    public const CHECKOUT_DO_PRE = 'coreshop.checkout.do.pre';

    public const CHECKOUT_DO_POST = 'coreshop.checkout.do.post';

    public const CHECKOUT_PAYMENT_PRE = 'coreshop.checkout.payment.pre';
}
