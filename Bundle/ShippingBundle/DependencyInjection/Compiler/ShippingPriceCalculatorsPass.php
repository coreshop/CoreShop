<?php

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ShippingPriceCalculatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.shipping.price_calculators')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.shipping.price_calculators');

        $map = [];
        foreach ($container->findTaggedServiceIds('coreshop.shipping.price_calculator') as $id => $attributes) {
            if (!isset($attributes[0]['priority']) || !isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged PriceCalculator `'.$id.'` needs to have `priority`, `type` attributes.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];
            $registry->addMethodCall('register', [$attributes[0]['type'], $attributes[0]['priority'], new Reference($id)]);
        }

        $container->setParameter('coreshop.shipping.price_calculators', $map);
    }
}
