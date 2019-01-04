<?php

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Order\Repository\CartItemRepositoryInterface;

class CartItemRepository extends PimcoreRepository implements CartItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCartItemsByProductId($productId)
    {
        $list = $this->getList();
        $list->setCondition('product__id = ?', [$productId]);
        $list->load();

        return $list->getObjects();
    }
}
