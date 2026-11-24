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

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class RuleRecursionGuard implements RuleRecursionGuardInterface
{
    /**
     * Rules currently being evaluated, per scope and subject, in evaluation order.
     *
     * @var array<string, array<string, RuleInterface>>
     */
    private array $stacks = [];

    public function __construct(
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function guard(
        string $scope,
        RuleInterface $rule,
        ResourceInterface $subject,
        callable $callback,
        mixed $onRecursion,
    ): mixed {
        $stackKey = $scope . '|' . ResourceIdentifier::identify($subject);
        $ruleKey = ResourceIdentifier::identify($rule);

        if (isset($this->stacks[$stackKey][$ruleKey])) {
            $this->logRecursion($scope, $rule, $subject, $this->stacks[$stackKey], $ruleKey);

            return $onRecursion;
        }

        $this->stacks[$stackKey][$ruleKey] = $rule;

        try {
            return $callback();
        } finally {
            unset($this->stacks[$stackKey][$ruleKey]);

            if ([] === $this->stacks[$stackKey]) {
                unset($this->stacks[$stackKey]);
            }
        }
    }

    /**
     * @param array<string, RuleInterface> $stack
     */
    private function logRecursion(
        string $scope,
        RuleInterface $rule,
        ResourceInterface $subject,
        array $stack,
        string $ruleKey,
    ): void {
        $keys = array_keys($stack);
        $cycle = array_slice($stack, (int) array_search($ruleKey, $keys, true));
        $cycle[] = $rule;

        $path = ResourceIdentifier::describeRulePath(array_values($cycle));

        $this->logger->warning(
            sprintf('CoreShop Rules: circular rule reference detected, evaluation stopped: %s', $path),
            [
                'scope' => $scope,
                'rule' => ResourceIdentifier::identify($rule),
                'subject' => ResourceIdentifier::identify($subject),
                'path' => $path,
            ],
        );
    }
}
