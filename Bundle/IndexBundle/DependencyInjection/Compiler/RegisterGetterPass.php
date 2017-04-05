<?php

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterGetterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.index.getter') || !$container->has('coreshop.form_registry.index.getter')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.index.getter');
        $formRegistry = $container->getDefinition('coreshop.form_registry.index.getter');

        $map = [];
        foreach ($container->findTaggedServiceIds('coreshop.index.getter') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['form-type'])) {
                throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `type`, `form-type` attributes.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
            $formRegistry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form-type']]);
        }

        $container->setParameter('coreshop.index.getters', $map);
    }
}
