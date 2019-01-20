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

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Core\Index\Extensions\ProductClassExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterIndexProductExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('coreshop.registry.index.extensions')) {
            return;
        }

        $stackId = 'coreshop.stack.product.pimcore_class_names';

        if ($container->hasParameter($stackId)) {
            $registry = $container->getDefinition('coreshop.registry.index.extensions');

            $stack = $container->getParameter($stackId);

            foreach ($stack as $class) {
                $definitionId = sprintf('%s.%s', 'coreshop.index.extension.product', strtolower($class));
                $definition = new Definition(ProductClassExtension::class);
                $definition->setArguments([
                    $class,
                ]);

                $container->setDefinition($definitionId, $definition);
                $registry->addMethodCall('register', [$class, new Reference($definitionId)]);
            }
        }
    }
}
