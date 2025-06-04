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

namespace CoreShop\Component\Registry;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class PrioritizedCompositeServicePass implements CompilerPassInterface
{
    public function __construct(
        private string $serviceId,
        private string $compositeId,
        private string $tagName,
        private string $methodName,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition($this->compositeId) && !$container->hasAlias($this->compositeId)) {
            return;
        }

        $this->injectTaggedServicesIntoComposite($container);
        $this->addAliasForCompositeIfServiceDoesNotExist($container);
    }

    private function injectTaggedServicesIntoComposite(ContainerBuilder $container): void
    {
        $channelContextDefinition = $container->findDefinition($this->compositeId);

        $taggedServices = $container->findTaggedServiceIds($this->tagName);
        foreach ($taggedServices as $id => $tags) {
            $this->addMethodCalls($channelContextDefinition, $id, $tags);
        }
    }

    private function addAliasForCompositeIfServiceDoesNotExist(ContainerBuilder $container): void
    {
        if ($container->has($this->serviceId)) {
            return;
        }

        $container->setAlias($this->serviceId, $this->compositeId)->setPublic(true);
    }

    private function addMethodCalls(Definition $channelContextDefinition, string $id, array $tags): void
    {
        foreach ($tags as $attributes) {
            $this->addMethodCall($channelContextDefinition, $id, $attributes);
        }
    }

    private function addMethodCall(Definition $channelContextDefinition, string $id, array $attributes): void
    {
        $arguments = [new Reference($id)];

        if (isset($attributes['priority'])) {
            $arguments[] = $attributes['priority'];
        }

        $channelContextDefinition->addMethodCall($this->methodName, $arguments);
    }
}
