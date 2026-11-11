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

use CoreShop\Component\Rule\Model\RuleInterface;

interface RuleCycleDetectorInterface
{
    /**
     * Returns the reference path leading from $rule back to itself (e.g. [A, B, A]),
     * or null if $rule does not reference itself, neither directly nor through other rules.
     *
     * @return RuleInterface[]|null
     */
    public function findCycle(RuleInterface $rule): ?array;
}
