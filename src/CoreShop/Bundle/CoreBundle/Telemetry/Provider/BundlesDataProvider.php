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
use CoreShop\Component\Core\Telemetry\TelemetryDataProviderInterface;

/**
 * Reports every installed CoreShop package plus every package of type
 * `pimcore-bundle`. The client carries no product knowledge; mapping packages to
 * products happens in the license portal.
 */
final class BundlesDataProvider implements TelemetryDataProviderInterface
{
    private const string VENDOR_PREFIX = 'coreshop/';

    private const string BUNDLE_TYPE = 'pimcore-bundle';

    public function provide(): array
    {
        $names = [];

        foreach (InstalledVersions::getInstalledPackages() as $name) {
            if (str_starts_with($name, self::VENDOR_PREFIX)) {
                $names[$name] = true;
            }
        }

        foreach (InstalledVersions::getInstalledPackagesByType(self::BUNDLE_TYPE) as $name) {
            $names[$name] = true;
        }

        $names = array_keys($names);
        sort($names);

        $bundles = [];
        foreach ($names as $name) {
            $version = InstalledVersions::getPrettyVersion($name);

            // packages only known through "replace"/"provide" carry no version; the replacing package is reported instead
            if (null === $version) {
                continue;
            }

            $bundles[] = ['name' => $name, 'version' => $version];
        }

        return ['bundles' => $bundles];
    }
}
