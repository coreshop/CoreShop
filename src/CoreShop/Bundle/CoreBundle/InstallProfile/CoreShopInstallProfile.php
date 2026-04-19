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

use Pimcore\Bundle\GenericDataIndexBundle\PimcoreGenericDataIndexBundle;
use Pimcore\Bundle\GenericExecutionEngineBundle\PimcoreGenericExecutionEngineBundle;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\DatabaseEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\DoctrineMessengerEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\OpenSearchEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\Profile\DataSource\DataSourceInterface;
use Pimcore\Bundle\InstallBundle\Profile\InstallProfileInterface;
use Pimcore\Bundle\StudioBackendBundle\PimcoreStudioBackendBundle;
use Pimcore\Bundle\StudioUiBundle\PimcoreStudioUiBundle;

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
        // The Studio UI, Studio Backend, Generic Data Index and Generic Execution Engine bundles
        // have their own installers (schema, permissions, search indexes) that must run during the
        // Pimcore install step. CoreShop's own bundles are registered via composer / dependent-
        // bundle chains (see CoreShopResourceBundle::registerDependentBundles) and install their
        // resources via the dedicated `coreshop:install` command afterwards.
        return [
            PimcoreGenericDataIndexBundle::class,
            PimcoreGenericExecutionEngineBundle::class,
            PimcoreStudioBackendBundle::class,
            PimcoreStudioUiBundle::class,
        ];
    }

    public function getEnvVarDefinitions(): array
    {
        return [
            new DatabaseEnvVarDefinition(),
            // Pimcore 2026.1 requires exactly one SearchEngineDefinitionInterface and one
            // MessengerTransportDefinitionInterface per profile. OpenSearch is the default search
            // backend for CoreShop; the Doctrine transport reuses the app database so no separate
            // broker is needed.
            new OpenSearchEnvVarDefinition(),
            new DoctrineMessengerEnvVarDefinition(),
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
