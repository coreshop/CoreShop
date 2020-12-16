<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Registry;

use CoreShop\Component\Registry\PrioritizedServiceRegistry;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class RegisterSimpleRegistryTypePass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $registry;

    /**
     * @var string
     */
    protected $parameter;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @param string $registry
     * @param string $parameter
     * @param string $tag
     */
    public function __construct($registry, $parameter, $tag)
    {
        $this->registry = $registry;
        $this->parameter = $parameter;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->registry)) {
            return;
        }

        $registry = $container->getDefinition($this->registry);
        $registryInterfaces = class_implements($registry->getClass());
        $isPrioritizedRegistry = false;

        if ($registryInterfaces && in_array(PrioritizedServiceRegistryInterface::class, $registryInterfaces, true)) {
            $isPrioritizedRegistry = true;
        }

        $map = [];
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            foreach ($attributes as $tag) {
                if (!isset($tag['type'])) {
                    $tag['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
                }

                $map[$tag['type']] = $tag['type'];

                if ($isPrioritizedRegistry) {
                    $registry->addMethodCall('register', [$tag['type'], $tag['priority'] ?? 1000, new Reference($id)]);
                }
                else {
                    $registry->addMethodCall('register', [$tag['type'], new Reference($id)]);
                }
            }
        }

        $container->setParameter($this->parameter, $map);
    }
}

class_alias(RegisterSimpleRegistryTypePass::class, 'CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterSimpleRegistryTypePass');
