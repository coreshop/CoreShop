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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Notification\Rule\Condition;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

abstract class AbstractConditionChecker implements NotificationConditionCheckerInterface
{
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, array $params = []): bool
    {
        if ($subject instanceof NotificationRuleInterface) {
            throw new \InvalidArgumentException('Notification Rule Condition $subject needs to be an array with values subject and params');
        }

        if (!array_key_exists('params', $params)) {
            throw new \InvalidArgumentException('Notification Rule Condition $subject needs to be an array with values subject and params');
        }

        return $this->isNotificationRuleValid($subject, $params['params'], $configuration);
    }
}
