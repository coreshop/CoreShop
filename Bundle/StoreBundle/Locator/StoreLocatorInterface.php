<?php

namespace CoreShop\Bundle\StoreBundle\Locator;

use CoreShop\Component\Store\Model\Store;
use Pimcore\Model\Site;

interface StoreLocatorInterface
{
    /**
     * @return Store
     */
    public function getStore();

    public function getStoreForSite(Site $site);

    /**
     * @return Store
     */
    public function getDefaultStore();
}
