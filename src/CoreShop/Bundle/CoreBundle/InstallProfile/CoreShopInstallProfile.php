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

namespace CoreShop\Bundle\CoreBundle\InstallProfile;

use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\DatabaseEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\Profile\DataSource\DataSourceInterface;
use Pimcore\Bundle\InstallBundle\Profile\InstallProfileInterface;

/**
 * Minimal CoreShop install profile.
 *
 * Installs a bare Pimcore with the database wired up. Everything CoreShop-specific
 * (DB tables, Pimcore class definitions, permissions, documents, routes, …) is
 * handled afterwards by `bin/console coreshop:install`, which drives the resource
 * installer chain (see `CoreShop\Bundle\ResourceBundle\Installer\*`).
 *
 * Usage:
 *     vendor/bin/pimcore-install \
 *         --install-profile 'CoreShop\Bundle\CoreBundle\InstallProfile\CoreShopInstallProfile'
 *     bin/console coreshop:install
 */
final readonly class CoreShopInstallProfile implements InstallProfileInterface
{
    public function getName(): string
    {
        return 'CoreShop';
    }

    public function getDescription(): string
    {
        return 'Installs a bare Pimcore (database only). Run `bin/console coreshop:install` '
            . 'afterwards to set up CoreShop classes, permissions, and fixtures.';
    }

    public function getBundles(): array
    {
        // CoreShop pulls all its bundles in via composer requirements and dependent-bundle
        // registration (see CoreShopResourceBundle::registerDependentBundles). Nothing to
        // add here — the installer only needs Pimcore's own core bundles, which it always
        // enables regardless of this list.
        return [];
    }

    public function getEnvVarDefinitions(): array
    {
        return [
            new DatabaseEnvVarDefinition(),
        ];
    }

    public function getDataSource(): ?DataSourceInterface
    {
        return null;
    }

    public function getPostInstallCommands(): array
    {
        return [];
    }
}
