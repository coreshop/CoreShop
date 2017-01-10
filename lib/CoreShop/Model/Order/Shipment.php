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
use CoreShop\Helper\Zend\Action;
use CoreShop\Model\Base;
use CoreShop\Model\Carrier;
use CoreShop\Model\Configuration;
use CoreShop\Model\Currency;
use CoreShop\Model\NumberRange;
use CoreShop\Model\Order;
use CoreShop\Model\Shop;
use CoreShop\Model\TaxCalculator;
use CoreShop\Model\User;
use CoreShop\Tool\Wkhtmltopdf;
use Pimcore\Date;
use Pimcore\Logger;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Asset\Service;
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
 * @method static Object\Listing\Concrete getByShop ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTaxes ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByItems ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCustomer ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShippingAddress ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByBillingAddress ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByExtraInformation ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTrackingCode ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByWeight ($value, $limit = 0)
 */
class Shipment extends Base
{
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
     */
    public static function getNextShipmentNumber()
    {
        $number = NumberRange::getNextNumberForType('shipment');

        return self::getValidShipmentNumber($number);
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
     * @param $number
     *
     * @return string
     */
    public static function getValidShipmentNumber($number)
    {
        $prefix = Configuration::get('SYSTEM.SHIPMENT.PREFIX');
        $suffix = Configuration::get('SYSTEM.SHIPMENT.SUFFIX');

        if ($prefix) {
            $number = $prefix.$number;
        }

        if ($suffix) {
            $number = $number.$suffix;
        }

        return $number;
    }

    /**
     * get folder for Shipments
     *
     * @param Order $order
     * @param \DateTime $date
     *
     * @return Object\Folder
     */
    public static function getPathForNewShipment(Order $order, $date = null)
    {
        if (is_null($date)) {
            $date = new Carbon();
        }

        return Object\Service::createFolderByPath($order->getFullPath() . "/shipments/" . $date->format("Y/m/d"));
    }

    /**
     * @return null
     */
    public function getPathForItems()
    {
        return Object\Service::createFolderByPath($this->getFullPath().'/items/');
    }

    /**
     * Renders the Shipment to a PDF
     *
     * @throws Exception
     *
     * @return Document|bool
     */
    public function generate()
    {
        $locale = new \Zend_Locale($this->getOrder()->getLang());

        $params = [
            "order" => $this->getOrder(),
            "shipment" => $this,
            "language" => (string) $locale,
            "type" => "shipment"
        ];

        $forward = new Action();
        $html = $forward->action("shipment", "order-print", "CoreShop", $params);
        $header = $forward->action("header", "order-print", "CoreShop", $params);
        $footer = $forward->action("footer", "order-print", "CoreShop", $params);

        try {
            $pdfContent = Wkhtmltopdf::fromString($html, $header, $footer, ['options' => [Configuration::get('SYSTEM.SHIPMENT.WKHTML')]]);

            if ($pdfContent) {
                $fileName = 'shipment-'.$this->getShipmentNumber().'.pdf';
                $path = $this->getOrder()->getPathForShipments();

                $shipment = Document::getByPath($path.'/'.$fileName);

                if ($shipment instanceof Document) {
                    $shipment->delete();
                }

                $shipment = new Document();
                $shipment->setFilename($fileName);
                $shipment->setParent(Service::createFolderByPath($path));
                $shipment->setData($pdfContent);
                $shipment->save();

                return $shipment;
            }
        } catch (Exception $ex) {
            Logger::warn('wkhtmltopdf library not found, no shipment was generated');
        }

        return false;
    }

    /**
     * @return Order
     *
     * @throws ObjectUnsupportedException
     */
    public function getOrder()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Order $order
     *
     * @throws ObjectUnsupportedException
     */
    public function setOrder($order)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
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
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getLang()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $lang
     *
     * @throws ObjectUnsupportedException
     */
    public function setLang($lang)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Shop
     *
     * @throws ObjectUnsupportedException
     */
    public function getShop()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Shop $shop
     *
     * @throws ObjectUnsupportedException
     */
    public function setShop($shop)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Shipment\Item[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getItems()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Shipment\Item[] $items
     *
     * @throws ObjectUnsupportedException
     */
    public function setItems($items)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return User
     *
     * @throws ObjectUnsupportedException
     */
    public function getCustomer()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param User $customer
     *
     * @throws ObjectUnsupportedException
     */
    public function setCustomer($customer)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getShippingAddress()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $shippingAddress
     *
     * @throws ObjectUnsupportedException
     */
    public function setShippingAddress($shippingAddress)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getBillingAddress()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $billingAddress
     *
     * @throws ObjectUnsupportedException
     */
    public function setBillingAddress($billingAddress)
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
