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

namespace CoreShop\Component\Core\Notification\Rule\Condition\Order;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use Webmozart\Assert\Assert;

class BackendCreatedChecker extends AbstractConditionChecker
{
    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        /**
         * @var OrderInterface $subject
         */
        Assert::isInstanceOf($subject, OrderInterface::class);

        if ($configuration['backendCreated']) {
            return $subject->getBackendCreated();
        }

        return !$subject->getBackendCreated();
    }
}
