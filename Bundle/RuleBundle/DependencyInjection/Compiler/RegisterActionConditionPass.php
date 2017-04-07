<?php

namespace CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class RegisterActionConditionPass implements CompilerPassInterface
{
    /**
     * @return string
     */
    abstract protected function getIdentifier();

    /**
     * @return string
     */
    abstract protected function getTagIdentifier();

    /**
     * @return string
     */
    abstract protected function getRegistryIdentifier();

    /**
     * @return string
     */
    abstract protected function getFormRegistryIdentifier();

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->getRegistryIdentifier()) || !$container->has($this->getFormRegistryIdentifier())) {
            return;
        }

        $registry = $container->getDefinition($this->getRegistryIdentifier());
        $formRegistry = $container->getDefinition($this->getFormRegistryIdentifier());

        $map = [];
        foreach ($container->findTaggedServiceIds($this->getTagIdentifier()) as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['form-type'])) {
                throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `type`, `form-type` and `label` attributes.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
            $formRegistry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form-type']]);
        }

        $container->setParameter($this->getIdentifier(), $map);
    }
}
