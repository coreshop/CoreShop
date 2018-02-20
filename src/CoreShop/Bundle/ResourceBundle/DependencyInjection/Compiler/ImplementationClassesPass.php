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

final class ImplementationClassesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('coreshop.all.pimcore_classes') || !$container->hasParameter('coreshop.all.implementations')) {
            return;
        }

        $classes = $container->getParameter('coreshop.all.pimcore_classes');
        $implementations = $container->getParameter('coreshop.all.implementations');

        $classImplementations = [];
        $classImplementationsPimcoreClassName = [];
        $classImplementationsPimcoreClassId = [];

        foreach ($implementations as $implementation => $interface) {
            list($applicationName, $implementationName) = explode('.', $implementation);

            $classImplementations[$implementation] = [];
            $classImplementationsPimcoreClassName[$implementation] = [];
            $classImplementationsPimcoreClassId[$implementation] = [];

            foreach ($classes as $key => $definition) {
                if (!@class_exists($definition['classes']['model'])) {
                    continue;
                }

                if (in_array($interface, class_implements($definition['classes']['model']))) {
                    $classImplementations[$implementation][] = $definition['classes']['model'];

                    if (is_subclass_of($definition['classes']['model'], Concrete::class)) {
                        $fullClassName = $definition['classes']['model'];
                        $class = str_replace('Pimcore\\Model\\DataObject\\', '', $fullClassName);
                        $class = str_replace('\\', '', $class);

                        $classImplementationsPimcoreClassName[$implementation][] = $class;
                        $classImplementationsPimcoreClassId[$implementation][] = $fullClassName::classId();
                    }
                }
            }

            $container->setParameter(sprintf('%s.implementations.%s.fqcns', $applicationName, $implementationName), $classImplementations[$implementation]);
            $container->setParameter(sprintf('%s.implementations.%s.pimcore_class_names', $applicationName, $implementationName), $classImplementationsPimcoreClassName[$implementation]);
            $container->setParameter(sprintf('%s.implementations.%s.pimcore_class_ids', $applicationName, $implementationName), $classImplementationsPimcoreClassId[$implementation]);
        }

        $container->setParameter('coreshop.all.implementations.fqcns', $classImplementations);
        $container->setParameter('coreshop.all.implementations.pimcore_class_names', $classImplementationsPimcoreClassName);
        $container->setParameter('coreshop.all.implementations.pimcore_class_ids', $classImplementationsPimcoreClassId);
    }
}