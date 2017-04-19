<?php

namespace CoreShop\Component\Product\Repository;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRule;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface ProductSpecificPriceRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param ProductInterface $product
     * @return ProductSpecificPriceRule[]
     */
    public function findForProduct(ProductInterface $product);

}