<?php

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterInterpreterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.index.interpreter') || !$container->has('coreshop.form_registry.index.interpreter')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.index.interpreter');
        $formRegistry = $container->getDefinition('coreshop.form_registry.index.interpreter');

        $map = [];
        foreach ($container->findTaggedServiceIds('coreshop.index.interpreter') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['form-type'])) {
                throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `type`, `form-type` attributes.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
            $formRegistry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form-type']]);
        }

        $container->setParameter('coreshop.index.interpreters', $map);
    }
}
