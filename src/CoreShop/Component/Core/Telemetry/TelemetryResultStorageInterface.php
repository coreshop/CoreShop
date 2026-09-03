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

interface TelemetryResultStorageInterface
{
    /**
     * @param array<string, mixed> $response
     */
    public function store(array $response): void;

    /**
     * @return array<string, mixed>|null
     */
    public function get(): ?array;

    public function getLastPing(): ?int;

    public function getLastAttempt(): ?int;

    public function markAttempt(): void;
}
