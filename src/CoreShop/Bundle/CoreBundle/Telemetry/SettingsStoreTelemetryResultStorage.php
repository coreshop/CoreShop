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

use CoreShop\Component\Core\Telemetry\TelemetryResultStorageInterface;
use Pimcore\Model\Tool\SettingsStore;

final class SettingsStoreTelemetryResultStorage implements TelemetryResultStorageInterface
{
    public const string SETTINGS_SCOPE = 'coreshop';

    public const string KEY_LAST_PING = 'telemetry.last_ping';

    public const string KEY_LAST_ATTEMPT = 'telemetry.last_attempt';

    public const string KEY_LAST_RESPONSE = 'telemetry.last_response';

    public function store(array $response): void
    {
        $now = time();

        SettingsStore::set(self::KEY_LAST_RESPONSE, json_encode($response, \JSON_THROW_ON_ERROR), 'string', self::SETTINGS_SCOPE);
        SettingsStore::set(self::KEY_LAST_PING, $now, 'int', self::SETTINGS_SCOPE);
        SettingsStore::set(self::KEY_LAST_ATTEMPT, $now, 'int', self::SETTINGS_SCOPE);
    }

    public function get(): ?array
    {
        $data = SettingsStore::get(self::KEY_LAST_RESPONSE, self::SETTINGS_SCOPE)?->getData();

        if (!is_string($data) || '' === $data) {
            return null;
        }

        try {
            $decoded = json_decode($data, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    public function getLastPing(): ?int
    {
        return $this->getInt(self::KEY_LAST_PING);
    }

    public function getLastAttempt(): ?int
    {
        return $this->getInt(self::KEY_LAST_ATTEMPT);
    }

    public function markAttempt(): void
    {
        SettingsStore::set(self::KEY_LAST_ATTEMPT, time(), 'int', self::SETTINGS_SCOPE);
    }

    private function getInt(string $key): ?int
    {
        $data = SettingsStore::get($key, self::SETTINGS_SCOPE)?->getData();

        if (null === $data) {
            return null;
        }

        return (int) $data;
    }
}
