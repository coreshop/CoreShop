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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Document;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception\ObjectUnsupportedException;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Base;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Service;


use Pimcore\Model\Object;

/**
 * Class Item
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice
 *
 * @method static Object\Listing\Concrete getByOrderItem ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByProduct ($value, $limit = 0)
 */
class Item extends Base
{
    /**
     * Get Document for Item
     *
     * @return null|\Pimcore\Model\Object\AbstractObject
     */
    public function getDocument()
    {
        $document = Service::getParentOfType($this, Order\Document::class);

        return $document instanceof Order\Document ? $document : null;
    }

    /**
     * @return Order\Item
     *
     * @throws ObjectUnsupportedException
     */
    public function getOrderItem()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Order\Item $orderItem
     *
     * @throws ObjectUnsupportedException
     */
    public function setOrderItem($orderItem)
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


    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getPrice()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $price
     *
     * @throws ObjectUnsupportedException
     */
    public function setPrice($price)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getPriceWithoutTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $priceWithoutTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setPriceWithoutTax($priceWithoutTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return int
     *
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
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getTotal()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $total
     *
     * @throws ObjectUnsupportedException
     */
    public function setTotal($total)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getTotalWithoutTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $totalWithoutTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setTotalWithoutTax($totalWithoutTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getTotalTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $totalTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setTotalTax($totalTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getTaxes()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $taxes
     *
     * @throws ObjectUnsupportedException
     */
    public function setTaxes($taxes)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
