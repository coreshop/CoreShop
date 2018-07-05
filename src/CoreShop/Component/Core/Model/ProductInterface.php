<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Model\ProductInterface as BaseProductInterface;
use CoreShop\Component\SEO\Model\PimcoreSEOAwareInterface;
use CoreShop\Component\SEO\Model\SEOImageAwareInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;

interface ProductInterface extends BaseProductInterface, IndexableInterface, PurchasableInterface, StockableInterface, PimcoreSEOAwareInterface, SEOImageAwareInterface, SEOOpenGraphAwareInterface
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
     * @param \CoreShop\Component\Store\Model\StoreInterface|null $store
     *
     * @return int
     */
    public function getStorePrice(\CoreShop\Component\Store\Model\StoreInterface $store = null);

    /**
     * @param $price
     * @param \CoreShop\Component\Store\Model\StoreInterface|null $store
     *
     * @return static
     */
    public function setStorePrice($price, \CoreShop\Component\Store\Model\StoreInterface $store = null);

    /**
     * @param TaxRuleGroupInterface $taxRule
     */
    public function setTaxRule($taxRule);

    /**
     * @return bool
     */
    public function getDigitalProduct();

    /**
     * @param bool $digitalProduct
     */
    public function setDigitalProduct($digitalProduct);
}
