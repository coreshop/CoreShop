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

use CoreShop\Component\Core\Telemetry\InstanceIdentifierProviderInterface;
use Pimcore\Model\Tool\SettingsStore;
use Symfony\Component\Uid\Uuid;

final class InstanceIdentifierProvider implements InstanceIdentifierProviderInterface
{
    public const string SETTINGS_SCOPE = 'coreshop';

    public const string SETTINGS_KEY = 'telemetry.instance_id';

    private ?string $identifier = null;

    public function __construct(
        private readonly ?string $pimcoreInstanceIdentifier,
        private readonly ?string $encryptionSecret,
    ) {
    }

    public function getIdentifier(): string
    {
        if (null !== $this->identifier) {
            return $this->identifier;
        }

        if (null !== $this->pimcoreInstanceIdentifier && '' !== trim($this->pimcoreInstanceIdentifier)) {
            return $this->identifier = trim($this->pimcoreInstanceIdentifier);
        }

        $stored = SettingsStore::get(self::SETTINGS_KEY, self::SETTINGS_SCOPE);
        $data = $stored?->getData();

        if (is_string($data) && '' !== $data) {
            return $this->identifier = $data;
        }

        $generated = Uuid::v6()->toBase58();
        SettingsStore::set(self::SETTINGS_KEY, $generated, 'string', self::SETTINGS_SCOPE);

        return $this->identifier = $generated;
    }

    public function getHashedIdentifier(): string
    {
        return hash('sha256', $this->getIdentifier());
    }

    public function getPimcoreInstanceId(): ?string
    {
        if (null === $this->pimcoreInstanceIdentifier || '' === trim($this->pimcoreInstanceIdentifier)) {
            return null;
        }

        if (null === $this->encryptionSecret || '' === $this->encryptionSecret) {
            return null;
        }

        return hash_hmac('sha256', trim($this->pimcoreInstanceIdentifier), $this->encryptionSecret);
    }
}
