<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Core\Index\Extensions\ProductClassExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterIndexProductExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('coreshop.registry.index.extensions')) {
            return;
        }

        $stackId = 'coreshop.stack.product.pimcore_class_names';

        if ($container->hasParameter($stackId)) {
            $registry = $container->getDefinition('coreshop.registry.index.extensions');

            /**
             * @var array $stack
             */
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
