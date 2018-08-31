<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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
