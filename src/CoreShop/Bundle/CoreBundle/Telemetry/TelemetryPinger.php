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

namespace CoreShop\Bundle\CoreBundle\Telemetry;

use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Component\Core\Telemetry\InstanceIdentifierProviderInterface;
use CoreShop\Component\Core\Telemetry\TelemetryDataProviderInterface;
use CoreShop\Component\Core\Telemetry\TelemetryPingerInterface;
use CoreShop\Component\Core\Telemetry\TelemetryResultStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TelemetryPinger implements TelemetryPingerInterface
{
    /**
     * Minimum seconds between two attempts, successful or not. Prevents hammering the
     * portal when pingIfStale() is triggered from the admin UI while the portal is down.
     */
    public const int ATTEMPT_BACKOFF = 900;

    private const array LIST_KEYS = ['hosts', 'bundles'];

    /**
     * @param iterable<TelemetryDataProviderInterface> $providers
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly iterable $providers,
        private readonly InstanceIdentifierProviderInterface $identifierProvider,
        private readonly TelemetryResultStorageInterface $storage,
        private readonly LoggerInterface $logger,
        private readonly bool $enabled,
        private readonly string $endpoint,
        private readonly float $timeout,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled && '' !== $this->endpoint;
    }

    public function buildPayload(): array
    {
        $payload = [
            'id' => $this->identifierProvider->getHashedIdentifier(),
            'pimcoreInstanceId' => $this->identifierProvider->getPimcoreInstanceId(),
        ];

        foreach ($this->providers as $provider) {
            $payload = $this->merge($payload, $provider->provide());
        }

        return array_filter($payload, static fn (mixed $value): bool => null !== $value && '' !== $value);
    }

    public function ping(): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $this->storage->markAttempt();

            $payload = $this->buildPayload();

            $response = $this->httpClient->request('POST', $this->endpoint, [
                'json' => $payload,
                'timeout' => $this->timeout,
                'max_duration' => $this->timeout + 1,
                'headers' => [
                    'User-Agent' => 'CoreShop/' . Version::getVersion(),
                    'X-CoreShop-Client' => Version::getVersion(),
                ],
            ]);

            $status = $response->getStatusCode();
            $data = $response->toArray(false);

            if ($status >= 400) {
                $this->logger->warning('CoreShop telemetry ping rejected by portal', ['status' => $status, 'response' => $data]);

                return null;
            }

            $this->storage->store($data);

            return $data;
        } catch (\Throwable $e) {
            $this->logger->warning('CoreShop telemetry ping failed', ['exception' => $e]);

            return null;
        }
    }

    public function pingIfStale(int $ttl = self::DEFAULT_TTL): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $now = time();
            $lastPing = $this->storage->getLastPing();
            $lastAttempt = $this->storage->getLastAttempt();

            $isFresh = null !== $lastPing && ($now - $lastPing) <= $ttl;
            $inBackoff = null !== $lastAttempt && ($now - $lastAttempt) < self::ATTEMPT_BACKOFF;

            if ($isFresh || $inBackoff) {
                return $this->storage->get();
            }
        } catch (\Throwable $e) {
            $this->logger->warning('CoreShop telemetry storage unavailable', ['exception' => $e]);

            return null;
        }

        return $this->ping() ?? $this->safeGet();
    }

    public function getLastResult(): array
    {
        try {
            return ['response' => $this->storage->get(), 'lastPing' => $this->storage->getLastPing()];
        } catch (\Throwable) {
            return ['response' => null, 'lastPing' => null];
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function safeGet(): ?array
    {
        try {
            return $this->storage->get();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $addition
     *
     * @return array<string, mixed>
     */
    private function merge(array $base, array $addition): array
    {
        foreach ($addition as $key => $value) {
            if (in_array($key, self::LIST_KEYS, true) && is_array($value) && is_array($base[$key] ?? null)) {
                $base[$key] = array_values(array_merge($base[$key], $value));

                continue;
            }

            $base[$key] = $value;
        }

        return $base;
    }
}
