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

/**
 * Knows which conditions/actions of a rule reference other rules. Used to detect
 * circular references before a rule gets saved.
 */
interface RuleReferenceResolverInterface
{
    public function supports(RuleInterface $rule): bool;

    /**
     * Returns the rules $rule directly references through its conditions and actions.
     *
     * @return iterable<RuleInterface>
     */
    public function getReferencedRules(RuleInterface $rule): iterable;
}
