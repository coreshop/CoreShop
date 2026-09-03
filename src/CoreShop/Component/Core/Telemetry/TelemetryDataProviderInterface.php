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

/**
 * Contributes a partial telemetry payload. Providers are collected via the
 * `coreshop.telemetry.provider` tag and merged shallowly; list-valued keys
 * (`hosts`, `bundles`) are concatenated.
 */
interface TelemetryDataProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function provide(): array;
}
