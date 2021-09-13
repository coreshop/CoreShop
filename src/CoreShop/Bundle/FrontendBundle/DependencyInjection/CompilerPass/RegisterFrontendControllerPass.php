<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\DependencyInjection\CompilerPass;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Bundle\PayumBundle\Factory\ConfirmOrderFactoryInterface;
use CoreShop\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use CoreShop\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Order\Payment\OrderPaymentProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFrontendControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $controllers = $container->getParameter('coreshop.frontend.controllers');

        foreach ($controllers as $key => $value) {
            $controllerKey = sprintf('coreshop.frontend.controller.%s', $key);
            $controllerClass = $container->getParameter($controllerKey);

            if ($container->hasDefinition($controllerClass)) {
                $customController = $container->getDefinition($controllerClass);

                $customController->addMethodCall('setContainer', [new Reference('service_container')]);
                $customController->addMethodCall('setTemplateConfigurator', [new Reference(TemplateConfiguratorInterface::class)]);

                $container->setDefinition($controllerKey, $customController)->setPublic(true);

                continue;
            }

            $controllerDefinition = new Definition($controllerClass);
            $controllerDefinition->addMethodCall('setContainer', [new Reference('service_container')]);
            $controllerDefinition->addMethodCall('setTemplateConfigurator', [new Reference(TemplateConfiguratorInterface::class)]);
            $controllerDefinition->setPublic(true);

            switch ($key) {
                case 'security':
                    $controllerDefinition->setArguments([
                        new Reference('security.authentication_utils'),
                        new Reference('form.factory'),
                        new Reference(ShopperContextInterface::class),
                    ]);
                break;

                case 'checkout':
                    $controllerDefinition->setArguments([
                        new Reference('coreshop.checkout_manager.factory')
                    ]);
                    break;

                case 'payment':
                    $controllerDefinition->setMethodCalls([
                        ['setContainer', [new Reference('service_container')]]
                    ]);
                    $controllerDefinition->setArguments([
                        new Reference(OrderPaymentProviderInterface::class),
                        new Reference('coreshop.repository.order'),
                        new Reference(GetStatusFactoryInterface::class),
                        new Reference(ResolveNextRouteFactoryInterface::class),
                        new Reference(ConfirmOrderFactoryInterface::class),
                    ]);
                    break;
            }

            $controllerDefinition->addTag('controller.service_arguments');

            $container->setDefinition($controllerClass, $controllerDefinition);
            $container->setAlias($controllerKey, $controllerClass)->setPublic(true);
        }
    }
}
