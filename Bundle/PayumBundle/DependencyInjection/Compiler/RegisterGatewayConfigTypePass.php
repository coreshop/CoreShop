<?php

namespace CoreShop\Bundle\PayumBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterGatewayConfigTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.form_registry.payum_gateway_config')) {
            return;
        }

        $formRegistry = $container->findDefinition('coreshop.form_registry.payum_gateway_config');
        $gatewayFactories = [];

        $gatewayConfigurationTypes = $container->findTaggedServiceIds('coreshop.gateway_configuration_type');

        foreach ($gatewayConfigurationTypes as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged gateway configuration type needs to have `type` attribute.');
            }

            $gatewayFactories[$attributes[0]['type']] = $attributes[0]['type'];

            $formRegistry->addMethodCall(
                'add',
                ['gateway_config', $attributes[0]['type'], $container->getDefinition($id)->getClass()]
            );
        }

        $gatewayFactories = array_merge($gatewayFactories, ['offline' => 'coreshop.payum_gateway_factory.offline']);
        ksort($gatewayFactories);

        $container->setParameter('coreshop.gateway_factories', $gatewayFactories);
    }
}
