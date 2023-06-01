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

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

abstract class AbstractConditionChecker implements PaymentConditionCheckerInterface
{
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, array $params = []): bool
    {
        if (!$subject instanceof PaymentProviderInterface) {
            throw new \InvalidArgumentException('Payment Rule Condition $subject needs to be an array with values shippable, address and carrier');
        }

        if (!array_key_exists('payable', $params)) {
            throw new \InvalidArgumentException('Payment Rule Condition $subject needs to be an array with a payable value');
        }

        return $this->isPaymentProviderRuleValid($subject, $params['payable'], $configuration);
    }
}
