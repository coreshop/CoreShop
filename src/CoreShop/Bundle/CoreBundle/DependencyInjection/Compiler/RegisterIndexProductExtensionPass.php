<?php

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
