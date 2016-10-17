<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Cart;

use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Base;
use CoreShop\Model\Cart;
use CoreShop\Model\Product;
use CoreShop\Model\User\Address;
use Pimcore\Model\Asset;
use Pimcore\Model\Object;

/**
 * Class Item
 * @package CoreShop\Model\Cart
 * 
 * @method static Object\Listing\Concrete getByAmount ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByProduct ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByExtraInformation ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByIsGiftItem ($value, $limit = 0)
 */
class Item extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopCartItem';

    /**
     * Calculates the total for the CartItem.
     *
     * @param $withTax boolean
     *
     * @return mixed
     */
    public function getTotal($withTax = true)
    {
        return $this->getAmount() * $this->getProductPrice($withTax);
    }

    /**
     * Get Cart Item Weight
     *
     * @return int
     */
    public function getWeight() {
        return $this->getAmount() * $this->getProduct()->getWeight();
    }

    /**
     * Get Cart Item is downloadable
     *
     * @return bool
     */
    public function getIsVirtualProduct() {
        return $this->getProduct()->getisVirtualProduct();
    }

    /**
     * Get CartItem Virtual Asset
     *
     * @return Asset
     */
    public function getVirtualAsset() {
        return $this->getProduct()->getVirtualAsset();
    }

    /**
     * Get Product Price
     *
     * @param bool $withTax
     * @return float|mixed
     */
    public function getProductPrice($withTax = true) {
        return $this->getProduct()->getPrice($withTax);
    }

    /**
     * Get Product Sales Price
     *
     * @param $withTax
     * @return float
     */
    public function getProductSalesPrice($withTax) {
        return $this->getProduct()->getSalesPrice($withTax);
    }

    /**
     * Get Products Wholesale Price
     *
     * @return float
     */
    public function getProductWholesalePrice() {
        return $this->getProduct()->getWholesalePrice();
    }

    /**
     * Get Products Retail Price
     *
     * @return float
     */
    public function getProductRetailPrice() {
        return $this->getProduct()->getRetailPrice();
    }

    /**
     * @return float
     */
    public function getProductRetailPriceWithTax() {
        return $this->getProduct()->getRetailPriceWithTax();
    }

    /**
     * @return float
     */
    public function getProductRetailPriceWithoutTax() {
        return $this->getProduct()->getRetailPriceWithoutTax();
    }

    /**
     * Get Tax Amount for Cart Item
     *
     * @return float
     */
    public function getTotalTax() {
       return $this->getAmount() * $this->getProductTaxAmount(false);
    }

    /**
     * @param bool $asArray
     * @return array|float
     */
    public function getProductTaxAmount($asArray = false)
    {
        return $this->getProduct()->getTaxAmount($asArray);
    }

    /**
     * Get Products Tax Calculator
     *
     * @param Address|null $address
     * @return bool|\CoreShop\Model\TaxCalculator
     */
    public function getProductTaxCalculator(Address $address = null) {
        return $this->getProduct()->getTaxCalculator($address);
    }


    /**
     * Get the Cart for this CartItem.
     *
     * @return \Pimcore\Model\Object\AbstractObject|void|null
     */
    public function getCart()
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof Cart) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return null;
    }
    
    /**
     * @return int
     * @throws ObjectUnsupportedException
     */
    public function getAmount()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param int $amount
     *
     * @throws ObjectUnsupportedException
     */
    public function setAmount($amount)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Product
     *
     * @throws ObjectUnsupportedException
     */
    public function getProduct()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Product $product
     *
     * @throws ObjectUnsupportedException
     */
    public function setProduct($product)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getExtraInformation()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $extraInformation
     *
     * @throws ObjectUnsupportedException
     */
    public function setExtraInformation($extraInformation)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getIsGiftItem()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $isGiftItem
     *
     * @throws ObjectUnsupportedException
     */
    public function setIsGiftItem($isGiftItem)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
