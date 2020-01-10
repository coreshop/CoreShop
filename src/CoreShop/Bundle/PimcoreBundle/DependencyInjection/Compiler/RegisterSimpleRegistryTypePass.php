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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

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

        $map = [];
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            if (!isset($attributes[0]['type'])) {
                $attributes[0]['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }

        $container->setParameter($this->parameter, $map);
    }
}
