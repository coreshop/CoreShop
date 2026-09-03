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

namespace CoreShop\Bundle\CoreBundle\Telemetry\Provider;

use CoreShop\Component\Core\Telemetry\TelemetryDataProviderInterface;
use Pimcore\Model\Site;
use Symfony\Component\Routing\RequestContext;

final class HostsDataProvider implements TelemetryDataProviderInterface
{
    private const array IGNORED_HOSTS = ['', 'localhost', '127.0.0.1', '::1'];

    /**
     * @param array<string, mixed> $pimcoreConfig
     */
    public function __construct(
        private readonly array $pimcoreConfig,
        private readonly ?RequestContext $requestContext = null,
    ) {
    }

    public function provide(): array
    {
        $hosts = [];

        $mainDomain = $this->pimcoreConfig['general']['domain'] ?? null;
        if (is_string($mainDomain)) {
            $hosts[] = $mainDomain;
        }

        try {
            foreach ((new Site\Listing())->load() as $site) {
                $hosts[] = $site->getMainDomain();
                $hosts = array_merge($hosts, $site->getDomains());
            }
        } catch (\Throwable) {
            // sites may be unavailable (e.g. no database during install); hosts are best-effort
        }

        $hosts = array_values(array_unique(array_filter(
            array_map(static fn (mixed $host): string => strtolower(trim((string) $host)), $hosts),
            static fn (string $host): bool => !in_array($host, self::IGNORED_HOSTS, true),
        )));

        $payload = ['hosts' => $hosts];

        $currentHost = strtolower(trim((string) $this->requestContext?->getHost()));
        if (!in_array($currentHost, self::IGNORED_HOSTS, true)) {
            $payload['currentDomain'] = $currentHost;
        }

        return $payload;
    }
}
