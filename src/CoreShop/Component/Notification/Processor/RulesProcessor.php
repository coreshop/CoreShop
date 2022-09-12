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

namespace CoreShop\Component\Notification\Processor;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class RulesProcessor implements RulesProcessorInterface
{
    public function __construct(
        private NotificationRuleRepositoryInterface $ruleRepository,
        private RuleValidationProcessorInterface $ruleValidationProcessor,
        private RuleApplierInterface $ruleApplier,
    ) {
    }

    public function applyRules(string $type, $subject, array $params = []): void
    {
        $rules = $this->ruleRepository->findForType($type);

        /**
         * @var NotificationRuleInterface $rule
         */
        foreach ($rules as $rule) {
            if ($this->ruleValidationProcessor->isValid($subject, $rule, ['params' => $params])) {
                $this->ruleApplier->applyRule($rule, $subject, $params);
            }
        }
    }
}
