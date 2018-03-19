<?php

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Core\Index\ProductClassHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProductHelperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('coreshop.registry.index.class_helpers')) {
            return;
        }

        $stackId = 'coreshop.stack.product.pimcore_class_names';

        if ($container->hasParameter($stackId)) {
            $registry = $container->getDefinition('coreshop.registry.index.class_helpers');

            $stack = $container->getParameter($stackId);

            foreach ($stack as $class) {
                $definitionId = sprintf('%s.%s', 'coreshop.index.class_helper.product', strtolower($class));
                $definition = new Definition(ProductClassHelper::class);
                $definition->setArguments([
                    $class
                ]);

                $container->setDefinition($definitionId, $definition);
                $registry->addMethodCall('register', [$class, new Reference($definitionId)]);
            }
        }
    }
}