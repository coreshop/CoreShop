<?php

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterCheckoutStepPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.checkout.steps')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.checkout.steps');

        $map = [];
        foreach ($container->findTaggedServiceIds('coreshop.registry.checkout.step') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `type`.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];
            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $registry->addMethodCall('register', [$attributes[0]['type'], $priority, new Reference($id)]);
        }

        $container->setParameter('coreshop.checkout.steps', $map);
    }
}
