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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

abstract class AbstractPimcoreExtension extends Extension
{
    /**
     * Registers a bundle's `pimcore_admin` configuration section.
     *
     * Two kinds of declaration are read, both consumed by the installers behind
     * `coreshop:resources:install`:
     *
     *  - `permissions`: Pimcore user-permission definitions. Every entry is prefixed with the
     *    application name, so `permissions: [order_list]` under the application `coreshop` declares
     *    `coreshop_permission_order_list`, installed into the group `coreshop_permission_group_coreshop`.
     *  - `install`: resources to install, keyed by installer type (`documents`, `image_thumbnails`,
     *    `sql`). Each type maps to a list of file references. Types are not validated here - a type with
     *    no installer behind it simply ends up in a container parameter nobody reads.
     *
     * The classic ExtJS admin's asset keys (`js`, `css`, `editmode_js`, `editmode_css`) are not read at
     * all, so bundles that still carry their pre-2026 configuration keep working unchanged.
     *
     * The section is named `pimcore_admin` for historical reasons only - it configures backend resources,
     * not the classic admin UI. Do not rename it and do not rename this method: bundles outside this
     * repository declare `pimcore_admin: { permissions: [...] }` and call `registerPimcoreResources()`
     * verbatim, and both names are kept deliberately so that code keeps working.
     */
    protected function registerPimcoreResources(string $applicationName, array $bundleResources, ContainerBuilder $container): void
    {
        if (array_key_exists('install', $bundleResources)) {
            foreach ($bundleResources['install'] as $type => $value) {
                $applicationParameter = sprintf('%s.pimcore.admin.install.%s', $applicationName, $type);
                $globalParameter = sprintf('coreshop.all.pimcore.admin.install.%s', $type);

                foreach ([$applicationParameter, $globalParameter] as $containerParameter) {
                    /**
                     * @var array $resources
                     */
                    $resources = $container->hasParameter($containerParameter) ? $container->getParameter($containerParameter) : [];

                    $container->setParameter($containerParameter, array_merge($resources, array_values($value)));
                }
            }
        }

        if (array_key_exists('permissions', $bundleResources)) {
            $applicationParameter = sprintf('%s.permissions', $applicationName);
            $globalParameter = 'coreshop.all.permissions';

            /**
             * @var string[] $applicationPermissions
             */
            $applicationPermissions = $container->hasParameter($applicationParameter) ? $container->getParameter($applicationParameter) : [];

            /**
             * @var array<string, string[]> $globalPermissions
             */
            $globalPermissions = $container->hasParameter($globalParameter) ? $container->getParameter($globalParameter) : [];

            $identifiers = [];

            foreach ($bundleResources['permissions'] as $permission) {
                $identifiers[] = sprintf('%s_permission_%s', $applicationName, $permission);
            }

            $globalPermissions[$applicationName] = array_values(array_unique(
                array_merge($globalPermissions[$applicationName] ?? [], $identifiers),
            ));

            $container->setParameter($globalParameter, $globalPermissions);
            $container->setParameter($applicationParameter, array_values(array_unique(
                array_merge($applicationPermissions, $identifiers),
            )));
        }
    }
}
