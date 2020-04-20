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

namespace CoreShop\Bundle\SEOBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SchemaRegistryServicePass implements CompilerPassInterface
{
    public const SCHEMA_GENERATOR_TAG = 'coreshop.seo.schema_generator';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.seo.schema_generator')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.seo.schema_generator');

        $map = [];
        foreach ($container->findTaggedServiceIds(static::SCHEMA_GENERATOR_TAG) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            if (!isset($attributes[0]['type'])) {
                $attributes[0]['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
            }

            if (!isset($attributes[0]['priority'])) {
                $attributes[0]['priority'] = 1000;
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], $attributes[0]['priority'], new Reference($id)]);
        }

        $container->setParameter('coreshop.seo.schema_generators', $map);
    }
}
