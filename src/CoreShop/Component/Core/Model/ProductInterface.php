<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface, IndexableInterface, PurchasableInterface
{
    /**
     * @return StoreInterface[]
     */
    public function getStores();

    /**
     * @param StoreInterface[] $stores
     */
    public function setStores($stores);

    /**
     * @return TaxRuleInterface
     */
    public function getTaxRule();

    /**
     * @param TaxRuleGroupInterface $taxRule
     */
    public function setTaxRule($taxRule);
}