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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class StackClassesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('coreshop.all.pimcore_classes') || !$container->hasParameter('coreshop.all.stack')) {
            return;
        }

        /**
         * @var array $classes
         */
        $classes = $container->getParameter('coreshop.all.pimcore_classes');

        /**
         * @var array $stack
         */
        $stack = $container->getParameter('coreshop.all.stack');

        $classStack = [];
        $classStackPimcoreClassName = [];
        $classStackPimcoreClassId = [];

        foreach ($stack as $alias => $interface) {
            list($applicationName, $name) = explode('.', $alias);

            $classStack[$alias] = [];
            $classStackPimcoreClassName[$alias] = [];
            $classStackPimcoreClassId[$alias] = [];

            foreach ($classes as $key => $definition) {
                if (!@interface_exists($definition['classes']['interface'])) {
                    continue;
                }

                if (in_array($interface, class_implements($definition['classes']['interface']))) {
                    $classStack[$alias][] = $definition['classes']['model'];

                    try {
                        $reflectionClass = new ReflectionClass($definition['classes']['model']);
                        $classStackPimcoreClassName[$alias][] = $reflectionClass->getDefaultProperties()['o_className'] ?? $definition['classes']['model'];
                    } catch (ReflectionException $e) {
                        $classStackPimcoreClassName[$alias][] = $definition['classes']['model'];
                    }
                }
            }

            $container->setParameter(sprintf('%s.stack.%s.fqcns', $applicationName, $name), $classStack[$alias]);
            $container->setParameter(sprintf('%s.stack.%s.pimcore_class_names', $applicationName, $name), $classStackPimcoreClassName[$alias]);
        }

        $container->setParameter('coreshop.all.stack.fqcns', $classStack);
        $container->setParameter('coreshop.all.stack.pimcore_class_names', $classStackPimcoreClassName);
    }
}
