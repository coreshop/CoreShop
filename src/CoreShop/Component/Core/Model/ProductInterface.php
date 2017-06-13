<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Product\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface, IndexableInterface
{
    /**
     * @return StoreInterface[]
     */
    public function getStores();

    /**
     * @param StoreInterface[] $stores
     */
    public function setStores($stores);
}