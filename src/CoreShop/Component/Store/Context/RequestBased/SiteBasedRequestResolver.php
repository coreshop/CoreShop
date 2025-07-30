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

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Context\SiteBasedResolverInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\Request;

final class SiteBasedRequestResolver implements RequestResolverInterface
{
    public function __construct(
        private SiteBasedResolverInterface $siteBasedResolver,
    ) {
    }

    public function findStore(Request $request): ?StoreInterface
    {
        $store = $this->siteBasedResolver->resolveSiteWithDefaultForStore(Site::isSiteRequest() ? Site::getCurrentSite() : null);

        if ($store) {
            return $store;
        }

        throw new StoreNotFoundException();
    }
}
