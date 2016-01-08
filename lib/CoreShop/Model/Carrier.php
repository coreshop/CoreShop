<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Model\Carrier\AbstractRange;
use CoreShop\Model\Carrier\DeliveryPrice;
use CoreShop\Plugin;
use CoreShop\Tool;

class Carrier extends AbstractModel
{
    const SHIPPING_METHOD_WEIGHT = "weight";
    const SHIPPING_METHOD_PRICE = "price";

    const RANGE_BEHAVIOUR_DEACTIVATE = "deactivate";
    const RANGE_BEHAVIOUR_LARGEST = "largest";

    public static $shippingMethods = array(self::SHIPPING_METHOD_PRICE, self::SHIPPING_METHOD_WEIGHT);
    public static $rangeBehaviours = array(self::RANGE_BEHAVIOUR_LARGEST, self::RANGE_BEHAVIOUR_DEACTIVATE);

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $delay;

    /**
     * @var boolean
     */
    public $needsRange;

    /**
     * @var int
     */
    public $grade;

    /**
     * @var int
     */
    public $image;

    /**
     * @var string
     */
    public $trackingUrl;

    /**
     * @var bool
     */
    public $is_free;

    /**
     * @var string
     */
    public $shippingMethod;

    /**
     * @var int
     */
    public $tax;

    /**
     * @var int
     */
    public $rangeBehaviour;

    /**
     * @var float
     */
    public $maxHeight;

    /**
     * @var float
     */
    public $maxWidth;

    /**
     * @var float
     */
    public $maxDepth;

    /**
     * @var float
     */
    public $maxWeight;

    /**
     * Save carrier
     *
     * @return mixed
     */
    public function save() {
        return $this->getDao()->save();
    }

    /**
     * get Carriere by ID
     *
     * @param $id
     * @return Carrier|null
     */
    public static function getById($id) {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    /**
     * get all carriers
     *
     * @return Carrier[]
     */
    public static function getAll() {
        $list = new Carrier\Listing();
        $list->setOrder("ASC");
        $list->setOrderKey("grade");

        return $list->getData();
    }

    /**
     * Get all available Carriers for cart
     *
     * @param Zone $zone
     * @param Cart|null $cart
     * @return CarrierInterface[]
     */
    public static function getCarriersForCart(Cart $cart = null, Zone $zone = null) {
        if(is_null($cart)) {
            $cart = Tool::prepareCart();
        }
        if(is_null($zone))
            $zone = Tool::getCountry()->getZone();

        $carriers = self::getAll();
        $availableCarriers = array();

        foreach($carriers as $carrier)
        {
            if(!$carrier->getMaxDeliveryPrice())
                continue;

            //Check for Ranges
            if($carrier->getRangeBehaviour() == self::RANGE_BEHAVIOUR_DEACTIVATE)
            {
                if($carrier->getShippingMethod() == self::SHIPPING_METHOD_PRICE) {
                    if (!$carrier->checkDeliveryPriceByValue($zone, $cart->getTotal())) {
                        continue;
                    }
                }

                if($carrier->getShippingMethod() == self::SHIPPING_METHOD_WEIGHT) {
                    if (!$carrier->checkDeliveryPriceByValue($zone, $cart->getTotalWeight())) {
                        continue;
                    }
                }
            }

            $carrierIsAllowed = true;

            //Check for max-size
            foreach($cart->getItems() as $item) {
                $product = $item->getProduct();

                if(($carrier->getMaxWidth() > 0 && $product->getWidth() > $carrier->getMaxWidth())
                    || ($carrier->getMaxHeight() > 0 && $product->getHeight() > $carrier->getMaxHeight())
                    || ($carrier->getMaxDepth() > 0 && $product->getDepth() > $carrier->getMaxDepth())
                    || ($carrier->getMaxWeight() > 0 && $product->getWeight() > $carrier->getMaxWeight())) {

                    $carrierIsAllowed = false;
                    break;
                }
            }

            if(!$carrierIsAllowed) {
                continue;
            }

            $availableCarriers[] = $carrier;
        }

        //TODO: allow carriers as plugins
        /*$providers = Plugin::getShippingProviders($zone, $cart);

        foreach($providers as $provider) {
            $availableCarriers[] = $provider;
        }*/

        return $availableCarriers;
    }

    /**
     * Get the Ranges for this carrier
     *
     * @return AbstractRange[]
     */
    public function getRanges() {
        if($this->getRangeBehaviour() == "weight")
            $list = new Carrier\RangeWeight\Listing();
        else
            $list = new Carrier\RangeWeight\Listing();

        $list->setCondition("carrierId=?", array($this->getId()));
        $list->load();

        return $list->getData();
    }

    /**
     * Get max possible delivery price for this carrier
     *
     * @param Zone $zone
     * @return float|bool
     */
    public function getMaxDeliveryPrice(Zone $zone = null) {
        if(is_null($zone))
            $zone = Tool::getCountry()->getZone();

        $ranges = $this->getRanges();

        if(count($ranges) === 0)
            return false;

        $maxPrice = 0;

        foreach($ranges as $range) {
            $price = $range->getPriceForZone($zone);

            if($price instanceof DeliveryPrice) {
                if ($price->getPrice() > $maxPrice)
                    $maxPrice = $price->getPrice();
            }
        }

        return $maxPrice;
    }

    /**
     * Get delivery Price for cart
     *
     * @param Zone $zone
     * @param Cart $cart
     * @return bool|float
     */
    public function getDeliveryPrice(Cart $cart, Zone $zone = null) {
        if(is_null($zone))
            $zone = Tool::getCountry()->getZone();

        if($this->getShippingMethod() === self::SHIPPING_METHOD_PRICE) {
            $value = $cart->getTotal();
        }
        else {
            $value = $cart->getTotalWeight();
        }

        $ranges = $this->getRanges();

        foreach($ranges as $range) {
            $price = $range->getPriceForZone($zone);

            if($price instanceof DeliveryPrice) {
                if ($value >= $range->getDelimiter1() && $value < $range->getDelimiter2()) {
                    return $price->getPrice() * (1 + ($this->getTax() / 100));
                }
            }
        }

        if($this->getRangeBehaviour() === self::RANGE_BEHAVIOUR_LARGEST) {
            return $this->getMaxDeliveryPrice($zone) * (1 + ($this->getTax() / 100));
        }

        return false;
    }


    /**
     * Check if carrier is available for zone and value
     *
     * @param Zone $zone
     * @param $value
     * @return bool
     */
    public function checkDeliveryPriceByValue(Zone $zone, $value) {
        $ranges = $this->getRanges();

        foreach($ranges as $range) {
            $price = $range->getPriceForZone($zone);

            if($price instanceof DeliveryPrice) {
                if ($value >= $range->getDelimiter1() && $value < $range->getDelimiter2()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param string $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return int
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param int $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @return int
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param int $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    /**
     * @param string $trackingUrl
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;
    }

    /**
     * @return boolean
     */
    public function isIsFree()
    {
        return $this->is_free;
    }

    /**
     * @param boolean $is_free
     */
    public function setIsFree($is_free)
    {
        $this->is_free = $is_free;
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param string $shippingMethod
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @return int
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param int $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    /**
     * @return int
     */
    public function getRangeBehaviour()
    {
        return $this->rangeBehaviour;
    }

    /**
     * @param int $rangeBehaviour
     */
    public function setRangeBehaviour($rangeBehaviour)
    {
        $this->rangeBehaviour = $rangeBehaviour;
    }

    /**
     * @return float
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * @param float $maxHeight
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = $maxHeight;
    }

    /**
     * @return float
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * @param float $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * @return float
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param float $maxDepth
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * @return float
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * @param float $maxWeight
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * @return boolean
     */
    public function getNeedsRange()
    {
        return $this->needsRange;
    }

    /**
     * @param boolean $needsRange
     */
    public function setNeedsRange($needsRange)
    {
        $this->needsRange = $needsRange;
    }
}
