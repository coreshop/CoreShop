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

namespace CoreShop\Bundle\TestBundle\InstallProfile;

use InvalidArgumentException;
use Pimcore\Bundle\GenericDataIndexBundle\PimcoreGenericDataIndexBundle;
use Pimcore\Bundle\GenericExecutionEngineBundle\PimcoreGenericExecutionEngineBundle;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\AmqpMessengerEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\DatabaseEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\DoctrineMessengerEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\ElasticsearchEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\Definitions\OpenSearchEnvVarDefinition;
use Pimcore\Bundle\InstallBundle\EnvVarDefinition\EnvVarDefinitionInterface;
use Pimcore\Bundle\InstallBundle\Profile\DataSource\DataSourceInterface;
use Pimcore\Bundle\InstallBundle\Profile\InstallProfileInterface;
use Pimcore\Bundle\StudioBackendBundle\PimcoreStudioBackendBundle;
use Pimcore\Bundle\StudioUiBundle\PimcoreStudioUiBundle;

/**
 * Install profile for the test apps of CoreShop bundles.
 *
 * Same shape as CoreShop\Bundle\CoreBundle\InstallProfile\CoreShopInstallProfile — a bare
 * Pimcore with the Studio stack, everything CoreShop-specific follows from
 * `bin/console coreshop:install` — but every part that differs between bundle repos is read
 * from the environment instead of being wired into each repo's CI workflow.
 *
 * Usage:
 *     CORESHOP_TEST_ADDITIONAL_BUNDLES='CoreShop\Bundle\B2B\CompanyBundle\CoreShopB2BCompanyBundle' \
 *     vendor/bin/pimcore-install \
 *         --install-profile 'CoreShop\Bundle\TestBundle\InstallProfile\CoreShopTestInstallProfile'
 *     bin/console coreshop:install
 *
 * Environment variables, all optional:
 *
 *  - CORESHOP_TEST_INSTALL_BUNDLES
 *      Comma-separated bundle FQCNs replacing the default list (the Studio stack below).
 *      Use this when a repo needs a completely different set; to only add or drop single
 *      bundles, prefer the two variables below.
 *
 *  - CORESHOP_TEST_ADDITIONAL_BUNDLES
 *      Comma-separated bundle FQCNs appended to the list. This is where a bundle repo names
 *      its own bundle so that the install profile registers and installs it, instead of the
 *      workflow running `pimcore:bundle:install` as a separate step.
 *
 *  - CORESHOP_TEST_SKIP_BUNDLES
 *      Comma-separated bundle FQCNs removed from the list, e.g. the Studio UI bundle for
 *      repos that ship no Studio plugin.
 *
 *  - CORESHOP_TEST_SEARCH_ENGINE
 *      'opensearch' (default) or 'elasticsearch'. Pimcore requires exactly one search engine
 *      definition per profile, so the backend can be switched but not turned off. The DSN
 *      itself comes from the backend's own variable (PIMCORE_OPENSEARCH_DSN /
 *      PIMCORE_ELASTICSEARCH_DSN).
 *
 *  - CORESHOP_TEST_MESSENGER_TRANSPORT
 *      'doctrine' (default) or 'amqp'. Doctrine reuses the app database, so a test run needs
 *      no separate broker.
 *
 * Note that the profile cannot influence anything the kernel resolves from the test app's own
 * config or .env files — those stay the repo's responsibility.
 */
final readonly class CoreShopTestInstallProfile implements InstallProfileInterface
{
    public const ENV_INSTALL_BUNDLES = 'CORESHOP_TEST_INSTALL_BUNDLES';

    public const ENV_ADDITIONAL_BUNDLES = 'CORESHOP_TEST_ADDITIONAL_BUNDLES';

    public const ENV_SKIP_BUNDLES = 'CORESHOP_TEST_SKIP_BUNDLES';

    public const ENV_SEARCH_ENGINE = 'CORESHOP_TEST_SEARCH_ENGINE';

    public const ENV_MESSENGER_TRANSPORT = 'CORESHOP_TEST_MESSENGER_TRANSPORT';

    /**
     * The bundles with their own installers (schema, permissions, search indexes) that have to
     * run during the Pimcore install step. CoreShop's own bundles are registered via composer
     * and install their resources via `coreshop:install` afterwards.
     *
     * @var list<class-string>
     */
    private const DEFAULT_BUNDLES = [
        PimcoreGenericDataIndexBundle::class,
        PimcoreGenericExecutionEngineBundle::class,
        PimcoreStudioBackendBundle::class,
        PimcoreStudioUiBundle::class,
    ];

    public function getName(): string
    {
        return 'CoreShop Test';
    }

    public function getDescription(): string
    {
        return 'Installs a bare Pimcore for the test app of a CoreShop bundle. The bundle list '
            . 'and the search/messenger backends are read from CORESHOP_TEST_* environment variables.';
    }

    public function getBundles(): array
    {
        $configured = $this->readList(self::ENV_INSTALL_BUNDLES);
        $additional = $this->readList(self::ENV_ADDITIONAL_BUNDLES) ?? [];
        $skipped = $this->readList(self::ENV_SKIP_BUNDLES) ?? [];

        $this->assertBundlesExist(
            $configured ?? [],
            self::ENV_INSTALL_BUNDLES,
        );
        $this->assertBundlesExist($additional, self::ENV_ADDITIONAL_BUNDLES);

        // A default is dropped when its package is not installed — the Studio bundles are
        // optional for bundle repos that ship no Studio plugin, and a repo should not have to
        // list them in CORESHOP_TEST_SKIP_BUNDLES to get a working install. Names that come
        // from the environment are never dropped silently, see assertBundlesExist().
        $bundles = $configured ?? array_filter(self::DEFAULT_BUNDLES, 'class_exists');
        $bundles = [...$bundles, ...$additional];

        return array_values(array_unique(array_diff($bundles, $skipped)));
    }

    public function getEnvVarDefinitions(): array
    {
        return [
            new DatabaseEnvVarDefinition(),
            $this->getSearchEngineDefinition(),
            $this->getMessengerTransportDefinition(),
        ];
    }

    public function getDataSource(): ?DataSourceInterface
    {
        return null;
    }

    /**
     * Empty on purpose: the bundle installers contribute their own post-install commands (the
     * Generic Data Index builds its search index that way). Anything that depends on
     * `coreshop:install` — the CoreShop and bundle class definitions, and therefore their search
     * indexes — cannot run here, because the install profile finishes before that command.
     */
    public function getPostInstallCommands(): array
    {
        return [];
    }

    /**
     * @param list<string> $bundles
     */
    private function assertBundlesExist(array $bundles, string $envVarName): void
    {
        foreach ($bundles as $bundle) {
            if (!class_exists($bundle)) {
                throw new InvalidArgumentException(sprintf(
                    '%s names bundle "%s", which does not exist. The CORESHOP_TEST_* bundle '
                    . 'variables take fully qualified class names, not bundle short names.',
                    $envVarName,
                    $bundle,
                ));
            }
        }
    }

    private function getSearchEngineDefinition(): EnvVarDefinitionInterface
    {
        $engine = $this->readValue(self::ENV_SEARCH_ENGINE) ?? 'opensearch';

        return match ($engine) {
            'opensearch' => new OpenSearchEnvVarDefinition(),
            'elasticsearch' => new ElasticsearchEnvVarDefinition(),
            default => throw new InvalidArgumentException(sprintf(
                '%s must be "opensearch" or "elasticsearch", got "%s".',
                self::ENV_SEARCH_ENGINE,
                $engine,
            )),
        };
    }

    private function getMessengerTransportDefinition(): EnvVarDefinitionInterface
    {
        $transport = $this->readValue(self::ENV_MESSENGER_TRANSPORT) ?? 'doctrine';

        return match ($transport) {
            'doctrine' => new DoctrineMessengerEnvVarDefinition(),
            'amqp' => new AmqpMessengerEnvVarDefinition(),
            default => throw new InvalidArgumentException(sprintf(
                '%s must be "doctrine" or "amqp", got "%s".',
                self::ENV_MESSENGER_TRANSPORT,
                $transport,
            )),
        };
    }

    /**
     * @return list<string>|null null when the variable is unset or empty, so that callers can
     *                          tell "not configured" from "configured as an empty list"
     */
    private function readList(string $name): ?array
    {
        $value = $this->readValue($name);

        if ($value === null) {
            return null;
        }

        $entries = array_values(array_filter(array_map(
            static fn (string $entry): string => trim($entry, " \t\n\r\0\x0B\\"),
            explode(',', $value),
        )));

        return $entries === [] ? null : $entries;
    }

    private function readValue(string $name): ?string
    {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? getenv($name);

        if (!\is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }
}
