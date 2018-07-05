<?php

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

interface CartItemRepositoryInterface extends PimcoreRepositoryInterface
{
    public function findCartItemsByProductId($productId);
}
