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

namespace CoreShop\Component\Payment;

final class PaymentTransitions
{
    public const string IDENTIFIER = 'coreshop_payment';

    public const string TRANSITION_CREATE = 'create';

    public const string TRANSITION_PROCESS = 'process';

    public const string TRANSITION_COMPLETE = 'complete';

    public const string TRANSITION_FAIL = 'fail';

    public const string TRANSITION_CANCEL = 'cancel';

    public const string TRANSITION_REFUND = 'refund';

    public const string TRANSITION_VOID = 'void';
}
