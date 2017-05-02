<?php

namespace CoreShop\Bundle\ProductBundle\Repository;

use CoreShop\Bundle\ResourceBundle\Repository\PimcoreRepository;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class ProductRepository extends PimcoreRepository implements ProductRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLatestByShop(StoreInterface $store, $count = 8)
    {
        $list = $this->getList();
        $list->setCondition('enabled = 1');
        $list->setLimit($count);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');

        return $list->getObjects();
    }
}