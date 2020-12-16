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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterTypeHintRegistriesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!method_exists($container, 'registerAliasForArgument')) {
            return;
        }

        foreach ($container->findTaggedServiceIds('coreshop.registry') as $id => $attributes) {
            foreach ($attributes as $tag) {
                if (!isset($tag['type_hint'])) {
                    throw new \InvalidArgumentException('Tagged Repository `'.$id.'` needs to have `type_hint` attributes');
                }

                $definition = $container->findDefinition($id);

                $implements = class_implements($definition->getClass());

                if (
                    !in_array(ServiceRegistryInterface::class, $implements) &&
                    !in_array(PrioritizedServiceRegistryInterface::class, $implements)
                ) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Registry needs to implement interface %s or %s, given %s',
                            ServiceRegistryInterface::class,
                            PrioritizedServiceRegistryInterface::class,
                            implode(', ', $implements)
                        )
                    );
                }

                $container->registerAliasForArgument(
                    $id,
                    ServiceRegistryInterface::class,
                    strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '],
                        $tag['type_hint']))).'Registry'
                );

                $container->registerAliasForArgument(
                    $id,
                    ServiceRegistry::class,
                    strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '],
                        $tag['type_hint']))).'Registry'
                );
            }
        }
    }
}
