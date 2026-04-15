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

namespace CoreShop\Component\Payment\Checker;

use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderRuleInterface;

interface PaymentProviderRuleCheckerInterface
{
    public function findValidPaymentProviderRule(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
    ): ?PaymentProviderRuleInterface;
}
