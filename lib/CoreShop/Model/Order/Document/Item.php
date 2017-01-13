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

namespace CoreShop\Model\Order\Document;

use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Base;
use CoreShop\Model\Order;
use CoreShop\Model\Product;
use CoreShop\Model\Service;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset;
use Pimcore\Model\Object;

/**
 * Class Item
 * @package CoreShop\Model\Order\Invoice
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
}
