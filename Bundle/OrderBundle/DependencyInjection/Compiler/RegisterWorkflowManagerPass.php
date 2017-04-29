<?php

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterWorkflowManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('coreshop.workflow.manager.registry')) {
            return;
        }

        foreach ($container->findTaggedServiceIds('coreshop.workflow.manager') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `type`.');
            }

            $container->getDefinition('coreshop.workflow.manager.registry')->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }
    }
}
