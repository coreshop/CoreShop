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

namespace CoreShop\Component\Rule\Recursion;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Model\Action;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

/**
 * Resolves references stored as rule id inside a condition/action configuration,
 * e.g. the "shippingRule" condition: ['shippingRule' => 5].
 */
final class ConfigurationRuleReferenceResolver implements RuleReferenceResolverInterface
{
    /**
     * @param class-string<RuleInterface> $ruleClass            rules handled by this resolver (and type of the referenced rules)
     * @param array<string, string>       $conditionTypes       condition type => configuration key holding the referenced rule id
     * @param array<string, string>       $actionTypes          action type => configuration key holding the referenced rule id
     * @param string[]                    $nestedConditionTypes condition types holding child conditions in their "conditions" configuration
     */
    public function __construct(
        private string $ruleClass,
        private RepositoryInterface $ruleRepository,
        private array $conditionTypes = [],
        private array $actionTypes = [],
        private array $nestedConditionTypes = ['nested'],
    ) {
    }

    public function supports(RuleInterface $rule): bool
    {
        return $rule instanceof $this->ruleClass;
    }

    public function getReferencedRules(RuleInterface $rule): iterable
    {
        yield from $this->resolveConditions($rule->getConditions());

        foreach ($rule->getActions() as $action) {
            $action = Action::denormalize($action);
            $configurationKey = $this->actionTypes[(string) $action->getType()] ?? null;

            if (null !== $configurationKey) {
                yield from $this->findRule($action->getConfiguration()[$configurationKey] ?? null);
            }
        }
    }

    /**
     * @param iterable<ConditionInterface|array> $conditions
     *
     * @return iterable<RuleInterface>
     */
    private function resolveConditions(iterable $conditions): iterable
    {
        foreach ($conditions as $condition) {
            $condition = Condition::denormalize($condition);
            $type = (string) $condition->getType();
            $configuration = $condition->getConfiguration() ?? [];

            if (isset($this->conditionTypes[$type])) {
                yield from $this->findRule($configuration[$this->conditionTypes[$type]] ?? null);
            }

            if (in_array($type, $this->nestedConditionTypes, true) && is_iterable($configuration['conditions'] ?? null)) {
                yield from $this->resolveConditions($configuration['conditions']);
            }
        }
    }

    /**
     * @return iterable<RuleInterface>
     */
    private function findRule(mixed $reference): iterable
    {
        if ($reference instanceof $this->ruleClass) {
            yield $reference;

            return;
        }

        if (!is_int($reference) && !(is_string($reference) && ctype_digit($reference))) {
            return;
        }

        $rule = $this->ruleRepository->find($reference);

        if ($rule instanceof $this->ruleClass) {
            yield $rule;
        }
    }
}
