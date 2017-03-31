<?php

namespace CoreShop\Bundle\StoreBundle\Helper;

use Pimcore\Model\Site;

class PimcoreSiteHelper implements PimcoreSiteHelperInterface {

    /**
     * @return boolean
     */
    public function isSiteRequest() {
        return Site::isSiteRequest();
    }

    /**
     * @return Site
     */
    public function getCurrentSite() {
        return Site::getCurrentSite();
    }
}