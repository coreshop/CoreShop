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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ImplementationClassesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('coreshop.pimcore.classes') || !$container->hasParameter('coreshop.implementations')) {
            return;
        }

        $classes = $container->getParameter('coreshop.pimcore');
        $implementations = $container->getParameter('coreshop.implementations');

        $classImplementations = [];

        foreach ($implementations as $implementation => $interface) {
            $classImplementations[$implementation] = [];

            foreach ($classes as $key => $definition) {
                if (!@class_exists($definition['classes']['model'])) {
                    continue;
                }

                if (in_array($interface, class_implements($definition['classes']['model']))) {
                    $classImplementations[$implementation][] = $definition['classes']['model'];
                }
            }

            $container->setParameter(sprintf('coreshop.implementations.%s', $implementation), $classImplementations[$implementation]);
        }

        $container->setParameter('coreshop.implementations.classes', $classImplementations);
    }
}
