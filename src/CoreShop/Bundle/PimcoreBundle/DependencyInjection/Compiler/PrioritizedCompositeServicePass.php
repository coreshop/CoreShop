<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class PrioritizedCompositeServicePass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $compositeId;

    /**
     * @var string
     */
    private $tagName;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @param string $serviceId
     * @param string $compositeId
     * @param string $tagName
     * @param string $methodName
     */
    public function __construct($serviceId, $compositeId, $tagName, $methodName)
    {
        $this->serviceId = $serviceId;
        $this->compositeId = $compositeId;
        $this->tagName = $tagName;
        $this->methodName = $methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->compositeId)) {
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

        $container->setAlias($this->serviceId, $this->compositeId);
    }

    /**
     * @param Definition $channelContextDefinition
     * @param string $id
     * @param array $tags
     */
    private function addMethodCalls(Definition $channelContextDefinition, $id, $tags)
    {
        foreach ($tags as $attributes) {
            $this->addMethodCall($channelContextDefinition, $id, $attributes);
        }
    }

    /**
     * @param Definition $channelContextDefinition
     * @param string $id
     * @param array $attributes
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
