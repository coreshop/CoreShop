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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\ResourcePermission;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ResourcePermissionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $resources = $container->getParameter('coreshop.resources');
        $globalParameter = 'coreshop.all.permissions';
        $globalPermissions = [];

        if ($container->hasParameter($globalParameter)) {
            /**
             * @var array $globalPermissions
             */
            $globalPermissions = $container->getParameter($globalParameter);
        }

        $permissions = [];

        foreach ($resources as $alias => $resourceConfig) {
            [$applicationName, $name] = explode('.', $alias);

            if (!isset($resourceConfig['permission'])) {
                continue;
            }

            if (!isset($permissions[$applicationName])) {
                $permissions[$applicationName] = [];
            }

            $permission = $resourceConfig['permission'];

            foreach (ResourcePermission::getAllPermissions() as $type) {
                $identifier = sprintf('%s_permission_%s_%s', $applicationName, $permission, $type);

                $permissions[$applicationName][$identifier] = true;
            }
        }

        foreach ($permissions as $applicationName => $appPermissions) {
            $appPermissions = array_keys($appPermissions);
            $applicationParameter = sprintf('%s.permissions', $applicationName);
            $applicationPermissions = [];

            if ($container->hasParameter($applicationParameter)) {
                /**
                 * @var array $applicationPermissions
                 */
                $applicationPermissions = $container->getParameter($applicationParameter);
            }

            $container->setParameter($applicationParameter, [...$applicationPermissions, ...$appPermissions]);

            if (!isset($globalPermissions[$applicationName])) {
                $globalPermissions[$applicationName] = [];
            }

            $globalPermissions[$applicationName] = [
                ...$globalPermissions[$applicationName],
                ...$appPermissions,
            ];
        }

        $container->setParameter($globalParameter, $globalPermissions);
    }
}
