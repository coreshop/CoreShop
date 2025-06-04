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

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use Webmozart\Assert\Assert;

final class StateTransitionChecker extends AbstractConditionChecker
{
    /**
     * @psalm-param class-string $interface
     */
    public function __construct(
        private string $interface,
        private string $workflowName,
    ) {
    }

    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        Assert::isInstanceOf($subject, $this->interface);

        if (isset($params['workflow'])) {
            if ($params['workflow'] !== $this->workflowName) {
                return false;
            }
        }

        if (isset($params['transition'])) {
            return $configuration['transition'] === $params['transition'];
        }

        return false;
    }
}
