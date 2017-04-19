<?php

namespace CoreShop\Component\Product\Repository;

use CoreShop\Component\Product\Model\ProductPriceRule;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface ProductPriceRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @return ProductPriceRule[]
     */
    public function findActive();

}