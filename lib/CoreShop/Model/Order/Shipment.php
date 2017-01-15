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

namespace CoreShop\Model\Order;

use Carbon\Carbon;
use CoreShop\Exception;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Carrier;
use CoreShop\Model\Mail\Rule;
use CoreShop\Model\Order;
use Pimcore\Date;
use Pimcore\Model\Object;
use Pimcore\Model\User as PimcoreUser;

/**
 * Class Shipment
 * @package CoreShop\Model\Order
 *
 * @method static Object\Listing\Concrete getByOrder($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShipmentDate ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShipmentNumber ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByLang ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCarrier ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTaxes ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTrackingCode ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByWeight ($value, $limit = 0)
 */
class Shipment extends Document
{
    /**
     * @var string
     */
    public static $documentType = 'shipment';

    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopOrderShipment';

    /**
     * Creates next Shipment Number.
     *
     * @return int|string
     * @deprecated Use getNextDocumentNumber instead. Will be removed with CoreShop 1.3
     */
    public static function getNextShipmentNumber()
    {
        return static::getNextDocumentNumber();
    }

    /**
     * @param $documentNumber
     * @return null|static
     */
    public static function findByDocumentNumber($documentNumber)
    {
        return static::findByShipmentNumber($documentNumber);
    }

    /**
     * Get Shipment by Shipment Number
     *
     * @param $shipmentNumber
     * @return static|null
     */
    public static function findByShipmentNumber($shipmentNumber)
    {
        $shipments = static::getByShipmentNumber($shipmentNumber);

        if (count($shipments->getObjects())) {
            return $shipments->getObjects()[0];
        }

        return null;
    }

    /**
     * Converts any Number to a valid ShipmentNumber with Suffix and Prefix.
     *
     * @param integer $number
     *
     * @deprecated Use getValidDocumentNumber instead. Will be removed with CoreShop 1.3;
     * @return string
     */
    public static function getValidShipmentNumber($number)
    {
        return static::getValidDocumentNumber($number);
    }

    /**
     * get folder for Shipments
     *
     * @param Order $order
     * @param \DateTime $date
     *
     * @deprecated Use getPathForDocuments instead. Will be removed with CoreShop 1.3
     * @return Object\Folder
     */
    public static function getPathForNewShipment(Order $order, $date = null)
    {
        return static::getPathForDocuments($order, $date);
    }

    /**
     * Create Shipping Tracking Url.
     *
     * @return string|null
     */
    public function getShippingTrackingUrl()
    {
        if ($this->getCarrier() instanceof Carrier) {
            if ($trackingUrl = $this->getCarrier()->getTrackingUrl()) {
                return sprintf($trackingUrl, $this->getTrackingCode());
            }
        }

        return null;
    }

    /**
     * @param Order $order
     * @param array $items
     * @param array $params
     *
     * @return static
     *
     * @throws Exception
     */
    public function fillDocument(Order $order, array $items, array $params = []) {
        if(!array_key_exists("carrier", $params)) {
            throw new Exception("Carrier does not exist");
        }

        if (!is_array($items)) {
            throw new Exception('Invalid Parameters');
        }

        if(!static::checkItemsAreProcessable($items)) {
            throw new Exception('You cannot ship more items than sold items');
        }

        $carrier = $params['carrier'];
        $trackingCode = array_key_exists('trackingCode', $params) ? $params['trackingCode'] : null;

        $this->setShipmentNumber(static::getNextDocumentNumber());
        $this->setOrder($order);

        if (\Pimcore\Config::getFlag('useZendDate')) {
            $this->setShipmentDate(Date::now());
        } else {
            $this->setShipmentDate(Carbon::now());
        }

        $this->setLang($order->getLang());
        $this->setParent($order->getPathForShipments());
        $this->setKey(\Pimcore\File::getValidFilename($this->getShipmentNumber()));
        $this->setCarrier($carrier);
        $this->setTrackingCode($trackingCode);
        $this->save();

        $totalWeight = 0;
        $items = $this->fillDocumentItems($items);

        foreach($items as $item) {
            if($item instanceof Order\Shipment\Item) {
                $totalWeight += $item->getWeight();
            }
        }

        $this->setItems($items);
        $this->setWeight($totalWeight);
        $this->setPublished(true);
        $this->save();

        //check orderState
        $order->checkOrderState();

        Rule::apply('shipment', $this);

        return $this;
    }

    /**
     * @param Item $orderItem
     * @param Document\Item $documentItem
     * @param $amount
     *
     * @return Order\Document\Item
     */
    protected function fillDocumentItem(Item $orderItem, Order\Document\Item $documentItem, $amount) {
        $documentItem = parent::fillDocumentItem($orderItem, $documentItem, $amount);

        if($documentItem instanceof Shipment\Item) {
            $documentItem->setWeight($orderItem->getProduct()->getWeight() * $documentItem->getAmount());
            $documentItem->save();
        }

        return $documentItem;
    }

    /**
     * @return Order\Shipment\Item
     */
    public function createItemInstance()
    {
        return Order\Shipment\Item::create();
    }

    /**
     * @return string
     */
    public function getDocumentDate()
    {
        return $this->getShipmentDate();
    }

    /**
     * @param Carbon|Date $documentDate
     */
    public function setDocumentDate($documentDate)
    {
        $this->setShipmentDate($documentDate);
    }

    /**
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->getShipmentNumber();
    }

    /**
     * @param string $documentNumber
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->setShipmentNumber($documentNumber);
    }

    /**
     * @return Carbon
     *
     * @throws ObjectUnsupportedException
     */
    public function getShipmentDate()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Carbon|Date $shipmentNumber
     *
     * @throws ObjectUnsupportedException
     */
    public function setShipmentDate($shipmentNumber)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getShipmentNumber()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $shipmentNumber
     *
     * @throws ObjectUnsupportedException
     */
    public function setShipmentNumber($shipmentNumber)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Carrier
     *
     * @throws ObjectUnsupportedException
     */
    public function getCarrier()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Carrier $carrier
     *
     * @throws ObjectUnsupportedException
     */
    public function setCarrier($carrier)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getTrackingCode()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $trackingCode
     *
     * @throws ObjectUnsupportedException
     */
    public function setTrackingCode($trackingCode)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
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
