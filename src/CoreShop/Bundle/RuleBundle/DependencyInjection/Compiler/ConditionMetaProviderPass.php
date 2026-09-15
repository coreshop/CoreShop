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

use CoreShop\Bundle\RuleBundle\Collector\ConditionMetaCollector;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Hands every service tagged "coreshop.rule.condition_meta_provider" to the ConditionMetaCollector.
 */
final class ConditionMetaProviderPass implements CompilerPassInterface
{
    public const string CONDITION_META_PROVIDER_TAG = 'coreshop.rule.condition_meta_provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(ConditionMetaCollector::class)) {
            return;
        }

        $providers = [];

        foreach (array_keys($container->findTaggedServiceIds(self::CONDITION_META_PROVIDER_TAG)) as $id) {
            $providers[] = new Reference($id);
        }

        $container->getDefinition(ConditionMetaCollector::class)->setArgument(0, new IteratorArgument($providers));
    }
}
