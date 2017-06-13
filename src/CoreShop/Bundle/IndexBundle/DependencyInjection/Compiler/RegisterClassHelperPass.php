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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterClassHelperPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.index.class_helpers')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.index.class_helpers');

        foreach ($container->findTaggedServiceIds('coreshop.index.class_helper') as $id => $attributes) {
            if (!isset($attributes[0]['class'])) {
                throw new \InvalidArgumentException('Tagged Service `' . $id . '` needs to have `class` attributes.');
            }

            $class = $attributes[0]['class'];

            $registry->addMethodCall('register', [$class, new Reference($id)]);
        }
    }
}
