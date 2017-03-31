<?php

namespace CoreShop\Bundle\StoreBundle\Helper;

use Pimcore\Model\Site;

interface PimcoreSiteHelperInterface {

    /**
     * @return boolean
     */
    public function isSiteRequest();

    /**
     * @return Site
     */
    public function getCurrentSite();
}