<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleConditionsValidationProcessor implements RuleConditionsValidationProcessorInterface
{
    private ServiceRegistryInterface $ruleRegistry;
    private string $type;

    public function __construct(ServiceRegistryInterface $ruleRegistry, string $type)
    {
        $this->ruleRegistry = $ruleRegistry;
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isValid(ResourceInterface $subject, RuleInterface $rule, $conditions, array $params = []): bool
    {
        if (!count($conditions)) {
            return true;
        }

        if (!$rule->getActive()) {
            return false;
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

        return $checker->isValid($subject, $rule, $condition->getConfiguration(), $params);
    }
}
