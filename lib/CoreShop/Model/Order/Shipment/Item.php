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

namespace CoreShop\Model\Order\Shipment;

use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Order;
use CoreShop\Model\Product;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Object;

/**
 * Class Item
 * @package CoreShop\Model\Order\Shipment
 *
 * @method static Object\Listing\Concrete getByPrice ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPriceWithoutTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByAmount ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTotalTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTotal ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByIsGiftItem ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTaxes ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByWeight ($value, $limit = 0)
 */
class Item extends Order\Document\Item
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopOrderShipmentItem';

    /**
     * Calculate Total of OrderItem without tax.
     *
     * @return float
     */
    public function getTotalWithoutTax()
    {
        return $this->getAmount() * $this->getPriceWithoutTax();
    }

    /**
     * Get Shipment for Shipment Item.
     *
     * @deprecated use getDocument instead. This method will be removed with CoreShop 1.3
     * @return null|\Pimcore\Model\Object\AbstractObject
     */
    public function getShipment()
    {
        return $this->getDocument();
    }

    /**
     * @return string|null Product Name
     */
    public function getProductName()
    {
        if ($this->getProduct() instanceof Product) {
            return $this->getProduct()->getName();
        }

        return null;
    }

    /**
     * @return \Pimcore\Model\Asset|null
     */
    public function getProductImage()
    {
        if ($this->getProduct() instanceof Product) {
            if ($this->getProduct()->getImage() instanceof Image) {
                return $this->getProduct()->getImage();
            };
        }

        return null;
    }

    /**
     * @return float
     *
     * @throws ObjectUnsupportedException
     */
    public function getWeight()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param float $weight
     *
     * @throws ObjectUnsupportedException
     */
    public function setWeight($weight)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
