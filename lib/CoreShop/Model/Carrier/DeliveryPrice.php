<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\Carrier;

use Pimcore\Model\AbstractModel;
use CoreShop\Tool;

class DeliveryPrice extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $carrier;

    /**
     * @var int
     */
    public $range;

    /**
     * @var string
     */
    public $rangeType;

    /**
     * @var float
     */
    public $price;

    public function save() {
        return $this->getResource()->save();
    }

    public static function getById($id) {
        try {
            $obj = new self;
            $obj->getResource()->getById($id);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    public static function getByCarrierAndRange($carrier, $range) {
        try {
            $obj = new self;
            $obj->getResource()->getByCarrierAndRange($carrier, $range);

            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
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
     * @return int
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param int $carrier
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @return int
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @param int $range
     */
    public function setRange($range)
    {
        $this->range = $range;
    }

    /**
     * @return string
     */
    public function getRangeType()
    {
        return $this->rangeType;
    }

    /**
     * @param string $rangeType
     */
    public function setRangeType($rangeType)
    {
        $this->rangeType = $rangeType;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}