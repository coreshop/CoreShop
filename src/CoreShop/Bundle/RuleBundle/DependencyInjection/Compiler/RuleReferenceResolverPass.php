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

namespace CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler;

use CoreShop\Component\Rule\Recursion\RuleCycleDetector;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Hands every service tagged "coreshop.rule.reference_resolver" to the RuleCycleDetector.
 */
final class RuleReferenceResolverPass implements CompilerPassInterface
{
    public const string RULE_REFERENCE_RESOLVER_TAG = 'coreshop.rule.reference_resolver';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(RuleCycleDetector::class)) {
            return;
        }

        $resolvers = [];

        foreach (array_keys($container->findTaggedServiceIds(self::RULE_REFERENCE_RESOLVER_TAG)) as $id) {
            $resolvers[] = new Reference($id);
        }

        $container->getDefinition(RuleCycleDetector::class)->setArgument(0, new IteratorArgument($resolvers));
    }
}
