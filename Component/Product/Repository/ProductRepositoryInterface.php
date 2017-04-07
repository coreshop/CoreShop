<?php

namespace CoreShop\Component\Product\Repository;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface ProductRepositoryInterface extends PimcoreRepositoryInterface {

    /**
     * @param StoreInterface $store
     * @param integer $count
     * @return ProductInterface[]
     */
    public function getLatestByShop(StoreInterface $store, $count = 8);

}