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

namespace CoreShop\Component\Payment\Validator;

use CoreShop\Component\Payment\Checker\PaymentProviderRuleCheckerInterface;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;

class PaymentProviderRuleValidator implements PaymentProviderRuleValidatorInterface
{
    public function __construct(
        private PaymentProviderRuleCheckerInterface $paymentProviderRuleChecker,
    ) {
    }

    public function isPaymentProviderRuleValid(PaymentProviderInterface $paymentProvider, PayableInterface $payable): bool
    {
        if (count($paymentProvider->getPaymentProviderRules()) === 0) {
            return true;
        }

        return null !== $this->paymentProviderRuleChecker->findValidPaymentProviderRule($paymentProvider, $payable);
    }
}
