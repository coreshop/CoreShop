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

namespace CoreShop\Component\Core\Payment\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Rule\Condition\AbstractConditionChecker;

class CountriesConditionChecker extends AbstractConditionChecker
{
    public function isPaymentProviderRuleValid(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $configuration,
    ): bool {
        if (!$payable instanceof OrderInterface) {
            return false;
        }
        $address = $payable->getInvoiceAddress();

        if (!$address instanceof AddressInterface) {
            return false;
        }

        $country = $address->getCountry();

        return in_array($country->getId(), $configuration['countries']);
    }
}
