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

namespace CoreShop\Bundle\CoreBundle\Maintenance;

use CoreShop\Component\Core\Telemetry\TelemetryPingerInterface;
use Pimcore\Maintenance\TaskInterface;

final class TelemetryTask implements TaskInterface
{
    public function __construct(
        private readonly TelemetryPingerInterface $pinger,
    ) {
    }

    public function execute(): void
    {
        // the pinger owns the 24h throttle, so running every maintenance cycle is cheap
        $this->pinger->pingIfStale();
    }
}
