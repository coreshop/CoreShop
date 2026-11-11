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

/**
 * Protects rule evaluation against rules that (directly or indirectly) reference themselves,
 * e.g. Shipping Rule A has a "shippingRule" condition pointing to B, and B one pointing to A.
 */
interface RuleRecursionGuardInterface
{
    public const string SCOPE_CONDITIONS = 'conditions';

    public const string SCOPE_ACTIONS_PRICE = 'actions_price';

    public const string SCOPE_ACTIONS_MODIFICATION = 'actions_modification';

    /**
     * Executes $callback while $rule is marked as "being evaluated" for $subject within $scope.
     *
     * If $rule is already being evaluated for the same $subject within the same $scope, the rules
     * reference each other in a circle: $callback is not executed and $onRecursion is returned instead.
     *
     * @template T
     *
     * @param callable(): T $callback
     * @param T             $onRecursion
     *
     * @return T
     */
    public function guard(
        string $scope,
        RuleInterface $rule,
        ResourceInterface $subject,
        callable $callback,
        mixed $onRecursion,
    ): mixed;
}
