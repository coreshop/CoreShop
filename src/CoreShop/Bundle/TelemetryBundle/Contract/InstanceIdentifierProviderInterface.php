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

namespace CoreShop\Bundle\TelemetryBundle\Contract;

interface InstanceIdentifierProviderInterface
{
    /**
     * Raw identifier: Pimcore's instance identifier if configured, otherwise a
     * UUID generated once and persisted. Never transmitted as-is.
     */
    public function getIdentifier(): string;

    /**
     * sha256 hex of the raw identifier. This is the `id` sent to the portal.
     */
    public function getHashedIdentifier(): string;

    /**
     * HMAC of Pimcore's instance identifier with the Pimcore encryption secret,
     * identical to what Pimcore's product registration uses. Null when either is missing.
     */
    public function getPimcoreInstanceId(): ?string;

    /**
     * Pimcore's instance identifier (`PIMCORE_INSTANCE_IDENTIFIER`) in clear text, so the
     * portal can match an installation with Pimcore's product registration. Null when not
     * configured; the generated UUID fallback is never exposed here.
     */
    public function getRawPimcoreIdentifier(): ?string;
}
