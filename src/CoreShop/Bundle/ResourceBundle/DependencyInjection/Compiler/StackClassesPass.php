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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Pimcore\Model\DataObject\Concrete;
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

        $classes = $container->getParameter('coreshop.all.pimcore_classes');
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
                if (!@class_exists($definition['classes']['model'])) {
                    continue;
                }

                if (in_array($interface, class_implements($definition['classes']['model']))) {
                    $classStack[$alias][] = $definition['classes']['model'];

                    if (is_subclass_of($definition['classes']['model'], Concrete::class)) {
                        $fullClassName = $definition['classes']['model'];
                        $class = str_replace('Pimcore\\Model\\DataObject\\', '', $fullClassName);
                        $class = str_replace('\\', '', $class);

                        $classStackPimcoreClassName[$alias][] = $class;
                        $classStackPimcoreClassId[$alias][] = $fullClassName::classId();
                    }
                }
            }

            $container->setParameter(sprintf('%s.stack.%s.fqcns', $applicationName, $name), $classStack[$alias]);
            $container->setParameter(sprintf('%s.stack.%s.pimcore_class_names', $applicationName, $name), $classStackPimcoreClassName[$alias]);
            $container->setParameter(sprintf('%s.stack.%s.pimcore_class_ids', $applicationName, $name), $classStackPimcoreClassId[$alias]);
        }

        $container->setParameter('coreshop.all.stack.fqcns', $classStack);
        $container->setParameter('coreshop.all.stack.pimcore_class_names', $classStackPimcoreClassName);
        $container->setParameter('coreshop.all.stack.pimcore_class_ids', $classStackPimcoreClassId);
    }
}
