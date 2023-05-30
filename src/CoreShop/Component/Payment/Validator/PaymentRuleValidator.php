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

use CoreShop\Component\Payment\Checker\PaymentRuleCheckerInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

class PaymentRuleValidator implements PaymentRuleValidatorInterface
{
    public function __construct(
        private PaymentRuleCheckerInterface $paymentRuleChecker,
    ) {
    }

    public function isPaymentRuleValid(PaymentProviderInterface $paymentProvider, ResourceInterface $subject = null): bool
    {
        if (count($paymentProvider->getPaymentRules()) === 0) {
            return true;
        }

        return null !== $this->paymentRuleChecker->findValidPaymentRule($paymentProvider, $subject);
    }
}
