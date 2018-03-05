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

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterPaymentSettingsFormsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('coreshop.gateway_factories') || !$container->has('coreshop.form_registry.payment.settings')) {
            return;
        }

        $payumFactories = $container->getParameter('coreshop.gateway_factories');
        $formRegistry = $container->getDefinition('coreshop.form_registry.payment.settings');

        foreach ($container->findTaggedServiceIds('coreshop.payment.form.settings') as $id => $attributes) {
            if (!isset($attributes[0]['payum-factory'])) {
                throw new \InvalidArgumentException('Tagged Service `' . $id . '` needs to have `payum-factory` attribute.');
            }

            $payumFactory = $attributes[0]['payum-factory'];

            if (!array_key_exists($payumFactory, $payumFactories)) {
                throw new \InvalidArgumentException(sprintf('You are trying to register a frontend-from for payum-factory %s which does not exist', $payumFactory));
            }

            $formRegistry->addMethodCall('add', [$payumFactory, 'default', $container->getDefinition($id)->getClass()]);
        }
    }
}
