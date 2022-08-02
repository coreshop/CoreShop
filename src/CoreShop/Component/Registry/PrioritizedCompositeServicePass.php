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

namespace CoreShop\Component\Registry;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class PrioritizedCompositeServicePass implements CompilerPassInterface
{
    public function __construct(private string $serviceId, private string $compositeId, private string $tagName, private string $methodName)
    {
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
