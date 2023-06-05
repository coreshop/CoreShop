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

namespace CoreShop\Component\Core\Payment\Rule\Condition;

use CoreShop\Component\Customer\Model\CustomerAwareInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Resource\Model\ResourceInterface;

final class CustomerGroupsConditionChecker extends AbstractConditionChecker
{
    public function isPaymentProviderRuleValid(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $configuration,
    ): bool {
        if (!$payable instanceof CustomerAwareInterface) {
            return false;
        }

        if (!$payable->getCustomer() instanceof CustomerInterface) {
            return false;
        }

        foreach ($payable->getCustomer()->getCustomerGroups() as $group) {
            if ($group instanceof ResourceInterface) {
                if (in_array($group->getId(), $configuration['customerGroups'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
