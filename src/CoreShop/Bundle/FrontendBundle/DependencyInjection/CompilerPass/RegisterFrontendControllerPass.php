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

namespace CoreShop\Bundle\FrontendBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterFrontendControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /**
         * @var array $controllers
         */
        $controllers = $container->getParameter('coreshop.frontend.controllers');

        foreach ($controllers as $key => $value) {
            $controllerKey = sprintf('coreshop.frontend.controller.%s', $key);

            if ($key === 'payment') {
                $serviceName = 'CoreShop\\Bundle\\PayumBundle\\Controller\\PaymentController';
            } else {
                $serviceName = sprintf('CoreShop\\Bundle\\FrontendBundle\\Controller\\%sController', ucfirst($key));
            }

            $controllerClass = (string) $container->getParameter($controllerKey);

            if ($container->hasDefinition($controllerClass)) {
                $customController = $container->getDefinition($controllerClass);

                $customController->addTag('container.service_subscriber');

                $container->setDefinition($serviceName, $customController)->setPublic(true);

                continue;
            }

            $controllerDefinition = new Definition($controllerClass);
            $controllerDefinition->setPublic(true);
            $controllerDefinition->addTag('controller.service_arguments');
            $controllerDefinition->addTag('container.service_subscriber');

            $container->setDefinition($serviceName, $controllerDefinition)->setPublic(true);

            if ($controllerClass !== $serviceName) {
                $container->setAlias($controllerClass, $serviceName)->setPublic(true);
            }
        }
    }
}
