<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
    private string $serviceId;
    private string $compositeId;
    private string $tagName;
    private string $methodName;

    public function __construct(string $serviceId, string $compositeId, string $tagName, string $methodName)
    {
        $this->serviceId = $serviceId;
        $this->compositeId = $compositeId;
        $this->tagName = $tagName;
        $this->methodName = $methodName;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->compositeId) && !$container->hasAlias($this->compositeId)) {
            return;
        }

        $this->injectTaggedServicesIntoComposite($container);
        $this->addAliasForCompositeIfServiceDoesNotExist($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function injectTaggedServicesIntoComposite(ContainerBuilder $container)
    {
        $channelContextDefinition = $container->findDefinition($this->compositeId);

        $taggedServices = $container->findTaggedServiceIds($this->tagName);
        foreach ($taggedServices as $id => $tags) {
            $this->addMethodCalls($channelContextDefinition, $id, $tags);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addAliasForCompositeIfServiceDoesNotExist(ContainerBuilder $container)
    {
        if ($container->has($this->serviceId)) {
            return;
        }

        $container->setAlias($this->serviceId, $this->compositeId)->setPublic(true);
    }

    /**
     * @param Definition $channelContextDefinition
     * @param string     $id
     * @param array      $tags
     */
    private function addMethodCalls(Definition $channelContextDefinition, $id, $tags)
    {
        foreach ($tags as $attributes) {
            $this->addMethodCall($channelContextDefinition, $id, $attributes);
        }
    }

    /**
     * @param Definition $channelContextDefinition
     * @param string     $id
     * @param array      $attributes
     */
    private function addMethodCall(Definition $channelContextDefinition, $id, $attributes)
    {
        $arguments = [new Reference($id)];

        if (isset($attributes['priority'])) {
            $arguments[] = $attributes['priority'];
        }

        $channelContextDefinition->addMethodCall($this->methodName, $arguments);
    }
}
