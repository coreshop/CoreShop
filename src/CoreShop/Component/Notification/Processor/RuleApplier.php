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

namespace CoreShop\Component\Notification\Processor;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

class RuleApplier implements RuleApplierInterface
{
    public function __construct(
        private ServiceRegistryInterface $actionServiceRegistry,
    ) {
    }

    public function applyRule(NotificationRuleInterface $rule, $subject, array $params): void
    {
        foreach ($rule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof NotificationRuleProcessorInterface) {
                $processor->apply($subject, $rule, $action->getConfiguration(), $params);
            }
        }
    }
}
