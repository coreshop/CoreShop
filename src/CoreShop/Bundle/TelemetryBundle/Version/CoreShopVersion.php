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

namespace CoreShop\Bundle\TelemetryBundle\Version;

use Composer\InstalledVersions;

/**
 * Resolves the CoreShop version without a hard dependency on CoreBundle, so the
 * telemetry bundle also works on installations that only use single CoreShop bundles.
 */
final class CoreShopVersion
{
    public static function get(): string
    {
        if (class_exists('\\CoreShop\\Bundle\\CoreBundle\\Application\\Version')) {
            return \CoreShop\Bundle\CoreBundle\Application\Version::getVersion();
        }

        foreach (['coreshop/core-shop', 'coreshop/telemetry-bundle'] as $package) {
            if (InstalledVersions::isInstalled($package)) {
                $version = InstalledVersions::getPrettyVersion($package);

                if (null !== $version) {
                    return $version;
                }
            }
        }

        return 'unknown';
    }
}
