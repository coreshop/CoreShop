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

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleConditionsValidationProcessor implements RuleConditionsValidationProcessorInterface
{
    public function __construct(
        private ServiceRegistryInterface $ruleRegistry,
        private string $type,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isValid(ResourceInterface $subject, RuleInterface $rule, $conditions, array $params = []): bool
    {
        if (!$rule->getActive()) {
            return false;
        }

        if (!count($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            if (!$this->isConditionValid($subject, $rule, $condition, $params)) {
                return false;
            }
        }

        return true;
    }

    public function isConditionValid(ResourceInterface $subject, RuleInterface $rule, ConditionInterface $condition, array $params = []): bool
    {
        /** @var ConditionCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($condition->getType());

        return $checker->isValid($subject, $rule, $condition->getConfiguration() ?? [], $params);
    }
}
