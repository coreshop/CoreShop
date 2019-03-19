<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\MenuBundle\DependencyInjection\CompilerPass;

use CoreShop\Bundle\MenuBundle\Builder;
use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class MenuBuilderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.menu.registry')) {
            return;
        }

        $registries = [];
        $types = [];
        $registeredTypes = [];

        $registry = $container->getDefinition('coreshop.menu.registry');

        $map = [];
        foreach ($container->findTaggedServiceIds('coreshop.menu') as $id => $attributes) {
            foreach ($attributes as $tag) {
                if (!isset($tag['type'], $tag['menu'])) {
                    throw new \InvalidArgumentException('Tagged Condition `' . $id . '` needs to have `type` and `menu`` attributes.');
                }

                $type = $tag['menu'];

                if (!array_key_exists($type, $registries)) {
                    $registries[$type] = new Definition(
                        ServiceRegistry::class,
                        [MenuBuilderInterface::class, 'menu-' . $type]
                    );


                    $builderService = new Definition(
                        Builder::class,
                        [new Reference('knp_menu.factory'), $type, new Reference('coreshop.menu.registry.'.$type)]
                    );
                    $builderService->addTag('knp_menu.menu_builder', [
                        'method' => 'createMenu',
                        'alias' => sprintf('coreshop.%s', $type)
                    ]);

                    $container->setDefinition('coreshop.menu.builder.'.$type, $builderService);
                    $container->setDefinition('coreshop.menu.registry.'.$type, $registries[$type]);

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
    }
}
