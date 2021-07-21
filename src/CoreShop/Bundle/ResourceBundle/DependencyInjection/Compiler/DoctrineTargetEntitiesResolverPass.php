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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Resolves given target entities with container parameters.
 * Usable only with *doctrine/orm* driver.
 */
final class DoctrineTargetEntitiesResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        try {
            $resources = $container->getParameter('coreshop.resources');
            $resolveTargetEntityListener = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
        } catch (InvalidArgumentException $exception) {
            return;
        }

        $interfaces = $this->resolve($resources);
        foreach ($interfaces as $interface => $model) {
            $resolveTargetEntityListener->addMethodCall('addResolveTargetEntity', [$interface, $model, []]);
        }

        $resolveTargetEntityListenerClass = $container->getParameterBag()->resolveValue($resolveTargetEntityListener->getClass());
        if (is_a($resolveTargetEntityListenerClass, EventSubscriber::class, true)) {
            if (!$resolveTargetEntityListener->hasTag('doctrine.event_subscriber')) {
                $resolveTargetEntityListener->addTag('doctrine.event_subscriber');
            }
        } elseif (!$resolveTargetEntityListener->hasTag('doctrine.event_listener')) {
            $resolveTargetEntityListener->addTag('doctrine.event_listener', ['event' => 'loadClassMetadata']);
        }
    }

    public function resolve(array $resources): array
    {
        $interfaces = [];

        foreach ($resources as $alias => $configuration) {
            $model = $this->getModel($alias, $configuration);

            foreach (class_implements($model) as $interface) {
                if ($interface === ResourceInterface::class) {
                    continue;
                }

                $interfaces[$interface][] = $model;
            }
        }

        $interfaces = array_filter($interfaces, static function (array $classes): bool {
            return count($classes) === 1;
        });

        $interfaces = array_map(static function (array $classes): string {
            return (string) current($classes);
        }, $interfaces);

        foreach ($resources as $alias => $configuration) {
            if (isset($configuration['classes']['interface'])) {
                $model = $this->getModel($alias, $configuration);
                $interface = $configuration['classes']['interface'];

                $interfaces[$interface] = $model;
            }
        }

        return $interfaces;
    }

    private function getModel(string $alias, array $configuration): string
    {
        if (!isset($configuration['classes']['model'])) {
            throw new \InvalidArgumentException(sprintf('Could not get model class from resource "%s".', $alias));
        }

        return $configuration['classes']['model'];
    }
}
