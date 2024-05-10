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

namespace CoreShop\Component\Payment\Rule\Condition;

use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;

class AmountConditionChecker extends AbstractConditionChecker
{
    public function isPaymentProviderRuleValid(PaymentProviderInterface $paymentProvider, PayableInterface $payable, array $configuration): bool
    {
        $minAmount = $configuration['minAmount'];
        $maxAmount = $configuration['maxAmount'];

        $totalAmount = $payable->getPaymentTotal();

        if ($totalAmount < 0) {
            $totalAmount = 0;
        }

        if ($minAmount > 0) {
            if ($totalAmount < $minAmount) {
                return false;
            }
        }

        if ($maxAmount > 0) {
            if ($totalAmount > $maxAmount) {
                return false;
            }
        }

        return true;
    }
}
