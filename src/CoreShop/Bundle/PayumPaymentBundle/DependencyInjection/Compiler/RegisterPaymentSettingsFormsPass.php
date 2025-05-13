<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PayumPaymentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterPaymentSettingsFormsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('coreshop.gateway_factories') || !$container->has('coreshop.form_registry.payment.settings')) {
            return;
        }

        $payumFactories = $container->getParameter('coreshop.gateway_factories');
        $formRegistry = $container->getDefinition('coreshop.form_registry.payment.settings');

        foreach ($container->findTaggedServiceIds('coreshop.payment.form.settings') as $id => $attributes) {
            foreach ($attributes as $tag) {
                if (!isset($tag['payum-factory'])) {
                    throw new \InvalidArgumentException('Tagged Service `' . $id . '` needs to have `payum-factory` attribute.');
                }

                $payumFactory = $tag['payum-factory'];

                if (!array_key_exists($payumFactory, $payumFactories)) {
                    throw new \InvalidArgumentException(sprintf(
                        'You are trying to register a frontend-from for payum-factory %s which does not exist',
                        $payumFactory,
                    ));
                }

                $formRegistry
                    ->addMethodCall('add', [$payumFactory, 'default', $container->getDefinition($id)->getClass()])
                ;
            }
        }
    }
}
