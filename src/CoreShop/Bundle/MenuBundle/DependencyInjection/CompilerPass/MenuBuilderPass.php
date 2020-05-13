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

declare(strict_types=1);

namespace CoreShop\Bundle\MenuBundle\DependencyInjection\CompilerPass;

use CoreShop\Bundle\MenuBundle\Builder;
use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class MenuBuilderPass implements CompilerPassInterface
{
    public const MENU_BUILDER_TAG = 'coreshop.menu';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.menu.registry')) {
            return;
        }

        if (!$container->has('coreshop.menu_provider.lazy_provider')) {
            return;
        }

        $menuBuilders = [];
        $registries = [];
        $types = [];
        $registeredTypes = [];

        $registry = $container->getDefinition('coreshop.menu.registry');

        $map = [];
        foreach ($container->findTaggedServiceIds(self::MENU_BUILDER_TAG) as $id => $attributes) {
            foreach ($attributes as $tag) {
                $definition = $container->findDefinition($id);

                if (!isset($attributes[0]['type'])) {
                    $attributes[0]['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
                }

                if (!isset($attributes[0]['menu'])) {
                    $attributes[0]['menu'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
                }

                $type = $tag['menu'];

                if (!array_key_exists($type, $registries)) {
                    $registries[$type] = new Definition(
                        ServiceRegistry::class,
                        [MenuBuilderInterface::class, 'menu-' . $type]
                    );

                    $builderService = new Definition(
                        Builder::class,
                        [new Reference('knp_menu.factory'), $type, new Reference('coreshop.menu.registry.' . $type)]
                    );

                    $container->setDefinition('coreshop.menu.builder.' . $type, $builderService);
                    $container->setDefinition('coreshop.menu.registry.' . $type, $registries[$type]);

                    $menuBuilders[sprintf('coreshop.%s', $type)] = [new ServiceClosureArgument(new Reference('coreshop.menu.builder.' . $type)), 'createMenu'];

                    $types[] = $type;
                }

                $map[$tag['menu']][$tag['type']] = $tag['type'];

                $fqtn = sprintf('%s.%s', $type, $tag['type']);

                $registries[$type]->addMethodCall('register', [$tag['type'], new Reference($id)]);
                $registry->addMethodCall('register', [$fqtn, new Reference($id)]);

                $registeredTypes[$fqtn] = $fqtn;
            }
        }

        foreach ($map as $type => $realMap) {
            $container->setParameter('coreshop.menus.' . $type, $realMap);
        }

        $container->setParameter('coreshop.menus.types', $types);
        $container->setParameter('coreshop.menus', $registeredTypes);

        $container->getDefinition('coreshop.menu_provider.lazy_provider')->replaceArgument(0, $menuBuilders);
    }
}
