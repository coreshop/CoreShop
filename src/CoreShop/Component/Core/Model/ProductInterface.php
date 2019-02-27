<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Model\ProductInterface as BaseProductInterface;
use CoreShop\Component\SEO\Model\PimcoreSEOAwareInterface;
use CoreShop\Component\SEO\Model\SEOImageAwareInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

interface ProductInterface extends BaseProductInterface, IndexableInterface, PurchasableInterface, StockableInterface, PimcoreSEOAwareInterface, SEOImageAwareInterface, SEOOpenGraphAwareInterface, QuantityRangePriceAwareInterface
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
     * @deprecated since 2.1.0
     * @use $product->getStoreValues()->getPrice() instead
     *
     * @param \CoreShop\Component\Store\Model\StoreInterface|null $store
     *
     * @return int
     */
    public function getStorePrice(\CoreShop\Component\Store\Model\StoreInterface $store = null);

    /**
     * @deprecated since 2.1.0
     * @use $product->setStoreValues() instead
     *
     * @param int                                                 $price
     * @param \CoreShop\Component\Store\Model\StoreInterface|null $store
     */
    public function setStorePrice($price, \CoreShop\Component\Store\Model\StoreInterface $store = null);

    /**
     * @param \CoreShop\Component\Store\Model\StoreInterface|null $store
     *
     * @return ProductStoreValuesInterface
     */
    public function getStoreValues(\CoreShop\Component\Store\Model\StoreInterface $store = null);

    /**
     * @param array|ProductStoreValuesInterface                   $storeValues
     * @param \CoreShop\Component\Store\Model\StoreInterface|null $store
     */
    public function setStoreValues($storeValues, \CoreShop\Component\Store\Model\StoreInterface $store = null);

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
