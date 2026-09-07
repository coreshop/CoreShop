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

use Pimcore\Http\Request\Resolver\SiteResolver;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;

class SiteIdProvider
{
    public function __construct(private readonly SiteResolver $siteResolver)
    {
    }

    public function getForRequest(?Request $request = null): SiteId
    {
        if ($this->siteResolver->isSiteRequest($request)) {
            $site = $this->siteResolver->getSite($request);
            if (!$site) {
                throw new \RuntimeException('Failed to fetch site for site request');
            }

            return SiteId::forSite($site);
        }

        return SiteId::forMainDomain();
    }

    /**
     * @return SiteId[]
     */
    public function getSiteIds(bool $includeMainDomain = true): array
    {
        $sites = new Site\Listing();
        $ids = [];

        if ($includeMainDomain) {
            $ids[] = SiteId::forMainDomain();
        }

        foreach ($sites->load() as $site) {
            $ids[] = SiteId::forSite($site);
        }

        return $ids;
    }
}
