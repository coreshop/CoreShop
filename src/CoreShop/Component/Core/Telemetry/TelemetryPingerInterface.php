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

namespace CoreShop\Component\Core\Telemetry;

interface TelemetryPingerInterface
{
    public const int DEFAULT_TTL = 86400;

    /**
     * Sends the ping unconditionally. Never throws; returns the decoded portal
     * response or null on failure / when disabled.
     *
     * @return array<string, mixed>|null
     */
    public function ping(): ?array;

    /**
     * Pings only when the last successful ping is older than $ttl seconds and the
     * last attempt is older than the attempt backoff; otherwise returns the stored result.
     *
     * @return array<string, mixed>|null
     */
    public function pingIfStale(int $ttl = self::DEFAULT_TTL): ?array;

    /**
     * @return array{response: array<string, mixed>|null, lastPing: int|null}
     */
    public function getLastResult(): array;

    /**
     * @return array<string, mixed>
     */
    public function buildPayload(): array;

    public function isEnabled(): bool;
}
