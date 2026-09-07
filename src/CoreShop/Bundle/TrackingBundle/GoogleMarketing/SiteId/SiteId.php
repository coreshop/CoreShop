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

namespace CoreShop\Bundle\TrackingBundle\GoogleMarketing\SiteId;

use Pimcore\Model\Site;

class SiteId
{
    public const string CONFIG_KEY_MAIN_DOMAIN = 'site_0';

    private function __construct(
        private readonly string $configKey,
        private readonly ?Site $site = null,
    ) {
    }

    public static function forMainDomain(): self
    {
        return new self(self::CONFIG_KEY_MAIN_DOMAIN);
    }

    public static function forSite(Site $site): self
    {
        return new self(sprintf('site_%s', $site->getId()), $site);
    }

    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }
}
