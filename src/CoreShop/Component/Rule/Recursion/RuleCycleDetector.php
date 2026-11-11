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

final class RuleCycleDetector implements RuleCycleDetectorInterface
{
    /**
     * @param iterable<RuleReferenceResolverInterface> $resolvers
     */
    public function __construct(
        private iterable $resolvers,
    ) {
    }

    public function findCycle(RuleInterface $rule): ?array
    {
        $visited = [ResourceIdentifier::identify($rule) => true];

        return $this->visit($rule, [$rule], $visited);
    }

    /**
     * Depth-first search for a path back to the first rule of $path. Rules already visited are
     * skipped: if there was no way back to the origin from them before, there is none now either.
     * That also keeps cycles between other rules (not involving the origin) from looping forever.
     *
     * @param RuleInterface[]     $path
     * @param array<string, true> $visited
     *
     * @return RuleInterface[]|null
     */
    private function visit(RuleInterface $current, array $path, array &$visited): ?array
    {
        $origin = $path[0];
        $originKey = ResourceIdentifier::identify($origin);

        foreach ($this->getReferencedRules($current) as $referenced) {
            $key = ResourceIdentifier::identify($referenced);

            if ($key === $originKey) {
                return [...$path, $origin];
            }

            if (isset($visited[$key])) {
                continue;
            }

            $visited[$key] = true;

            $cycle = $this->visit($referenced, [...$path, $referenced], $visited);

            if (null !== $cycle) {
                return $cycle;
            }
        }

        return null;
    }

    /**
     * @return iterable<RuleInterface>
     */
    private function getReferencedRules(RuleInterface $rule): iterable
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($rule)) {
                yield from $resolver->getReferencedRules($rule);
            }
        }
    }
}
