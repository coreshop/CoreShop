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

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Component\Core\Telemetry\TelemetryDataProviderInterface;

final class SystemDataProvider implements TelemetryDataProviderInterface
{
    public function __construct(
        private readonly string $kernelEnvironment,
    ) {
    }

    public function provide(): array
    {
        return [
            'environment' => $this->kernelEnvironment,
            'coreshop' => Version::getVersion(),
            'pimcore' => InstalledVersions::getPrettyVersion('pimcore/pimcore') ?? 'unknown',
            'php' => \PHP_VERSION,
            'timestamp' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(\DateTimeInterface::ATOM),
        ];
    }
}
