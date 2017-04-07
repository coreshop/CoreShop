<?php

namespace CoreShop\Bundle\StoreBundle\Helper;

use Pimcore\Model\Site;

interface PimcoreSiteHelperInterface
{
    /**
     * @return bool
     */
    public function isSiteRequest();

    /**
     * @return Site
     */
    public function getCurrentSite();
}
