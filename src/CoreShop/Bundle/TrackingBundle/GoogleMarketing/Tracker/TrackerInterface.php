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
 * Originally derived from pimcore/google-marketing-bundle (POCL).
 */

namespace CoreShop\Bundle\TrackingBundle\GoogleMarketing\Tracker;

use CoreShop\Bundle\TrackingBundle\GoogleMarketing\SiteId\SiteId;

interface TrackerInterface
{
    public function generateCode(?SiteId $siteId = null): ?string;

    public function addCodePart(string $code, ?string $block = null, bool $prepend = false, ?SiteId $siteId = null): void;
}
