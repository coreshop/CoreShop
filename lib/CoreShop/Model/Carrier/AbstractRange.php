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

namespace CoreShop\Model\Carrier;

use Pimcore\Model\AbstractModel;

use CoreShop\Model\Carrier;
use CoreShop\Model\Zone;
use CoreShop\Tool;

class AbstractRange extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $carrierId;

    /**
     * @var Carrier
     */
    public $carrier;

    /**
     * @var float
     */
    public $delimiter1;

    /**
     * @var float
     */
    public $delimiter2;

    public function save() {
        return $this->getDao()->save();
    }

    public function delete() {
        $prices = $this->getPrices();

        foreach($prices as $price) {
            $price->delete();
        }

        return $this->getDao()->delete();
    }

    /**
     * @param $rangeType
     * @return AbstractRange
     */
    public static function create($rangeType) {
        $className = "CoreShop\\Model\\Carrier\\" . ($rangeType == "weight" ? "RangeWeight" : "RangePrice");

        return new $className();
    }

    public static function getById($id, $rangeType) {
        try {
            $className = "CoreShop\\Model\\Carrier\\" . ($rangeType == "weight" ? "RangeWeight" : "RangePrice");

            $obj = new $className();
            $obj->getDao()->getById($id);

            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    /**
     * Get price for Zone
     *
     * @param Zone $zone
     * @return DeliveryPrice|null
     */
    public function getPriceForZone(Zone $zone) {
        return DeliveryPrice::getForCarrierInZone($this->getCarrier(), $this, $zone);
    }

    /**
     * Get price for Zone
     *
     * @param Zone $zone
     * @return DeliveryPrice|null
     */
    public function getPrices() {
        return DeliveryPrice::getByCarrierAndRange($this->getCarrier(), $this);
    }

    /**
     * @return float
     */
    public function getDelimiter2()
    {
        return $this->delimiter2;
    }

    /**
     * @param float $delimiter2
     */
    public function setDelimiter2($delimiter2)
    {
        $this->delimiter2 = $delimiter2;
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
     * @return Carrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param $carrier
     * @throws \Exception
     */
    public function setCarrier($carrier)
    {
        if(is_int($carrier))
            $carrier = Carrier::getById($carrier);

        if(!$carrier instanceof Carrier)
            throw new \Exception("\$carrier must be instance of Carrier");

        $this->carrier = $carrier;
        $this->carrierId = $carrier->getId();
    }

    /**
     * @return int
     */
    public function getCarrierId()
    {
        return $this->carrierId;
    }

    /**
     * @param $carrierId
     * @throws \Exception
     */
    public function setCarrierID($carrierId)
    {
        $carrier = Carrier::getById($carrierId);

        if(!$carrier instanceof Carrier) {
            $this->carrier = null;
            $this->carrierId = null;
        }
        else {
            $this->carrierId = $carrierId;
            $this->carrier = $carrier;
        }
    }

    /**
     * @return float
     */
    public function getDelimiter1()
    {
        return $this->delimiter1;
    }

    /**
     * @param float $delimiter1
     */
    public function setDelimiter1($delimiter1)
    {
        $this->delimiter1 = $delimiter1;
    }
}