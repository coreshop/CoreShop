<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\PayumPaymentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterGatewayConfigTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('coreshop.form_registry.payum_gateway_config')) {
            return;
        }

        $formRegistry = $container->findDefinition('coreshop.form_registry.payum_gateway_config');
        $gatewayFactories = [];

        $gatewayConfigurationTypes = $container->findTaggedServiceIds('coreshop.gateway_configuration_type');

        foreach ($gatewayConfigurationTypes as $id => $attributes) {
            $definition = $container->findDefinition($id);

            foreach ($attributes as $tags) {
                if (!isset($tags['type'])) {
                    $tags['type'] = Container::underscore(substr((string) strrchr($definition->getClass(), '\\'), 1));
                }

                $gatewayFactories[$tags['type']] = $tags['type'];

                $formRegistry->addMethodCall(
                    'add',
                    ['gateway_config', $tags['type'], $container->getDefinition($id)->getClass()],
                );
            }
        }

        $gatewayFactories = array_merge($gatewayFactories, ['offline' => 'coreshop.payum_gateway_factory.offline']);
        ksort($gatewayFactories);

        $container->setParameter('coreshop.gateway_factories', $gatewayFactories);

        // Build a map of gateway factory name → form type block prefix for Studio frontend.
        $gatewayBlockPrefixes = [];
        foreach ($gatewayConfigurationTypes as $id => $attributes) {
            $definition = $container->findDefinition($id);
            $class = $definition->getClass();

            foreach ($attributes as $tags) {
                if (!isset($tags['type'])) {
                    $tags['type'] = Container::underscore(substr((string) strrchr($class, '\\'), 1));
                }

                // Compute block prefix: same logic as Symfony AbstractType::getBlockPrefix()
                $shortName = substr((string) strrchr($class, '\\'), 1);
                $blockPrefix = Container::underscore(preg_replace('/Type$/', '', $shortName));
                $gatewayBlockPrefixes[$tags['type']] = $blockPrefix;
            }
        }

        $container->setParameter('coreshop.gateway_block_prefixes', $gatewayBlockPrefixes);
    }
}
