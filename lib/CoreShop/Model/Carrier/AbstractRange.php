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

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Carrier;
use CoreShop\Model\Zone;
use CoreShop\Tool;
use Pimcore\Cache;

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

    /**
     * Delete AbstractRange
     *
     * @return mixed
     */
    public function delete()
    {
        $prices = $this->getPrices();

        if(is_array($prices)) {
            foreach ($prices as $price) {
                $price->delete();
            }
        }

        return parent::delete();
    }

    /**
     * @param $rangeType
     * @return AbstractRange
     */
    public static function create($rangeType)
    {
        $className = "CoreShop\\Model\\Carrier\\" . ($rangeType == "weight" ? "RangeWeight" : "RangePrice");

        return new $className();
    }

    /**
     * Get Range by id
     *
     * @param $id
     * @param $rangeType
     * @return null
     */
    public static function getById($id, $rangeType)
    {
        $id = intval($id);

        if ($id < 1) {
            return null;
        }

        $cacheKey = "coreshop_" . $rangeType . "_" . $id;

        try {
            $range = \Zend_Registry::get($cacheKey);
            if (!$range) {
                throw new \Exception("RangeType in registry is null");
            }
            return $range;
        } catch (\Exception $e) {
            try {
                if (!$range = Cache::load($cacheKey)) {
                    $className = "CoreShop\\Model\\Carrier\\" . ($rangeType == "weight" ? "RangeWeight" : "RangePrice");

                    $range = new $className();
                    $range ->getDao()->getById($id);

                    \Zend_Registry::set($cacheKey, $range);
                    Cache::save($range, $cacheKey);
                } else {
                    \Zend_Registry::set($cacheKey, $range);
                }

                return $range;
            } catch (\Exception $e) {
                \Logger::warning($e->getMessage());
            }
        }

        return null;
    }

    /**
     * Get price for Zone
     *
     * @param Zone $zone
     * @return DeliveryPrice|null
     */
    public function getPriceForZone(Zone $zone)
    {
        return DeliveryPrice::getForCarrierInZone($this->getCarrier(), $this, $zone);
    }

    /**
     * Get price for Zone
     *
     * @return DeliveryPrice|null
     */
    public function getPrices()
    {
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
        if (!$this->carrier instanceof Carrier) {
            $this->carrier = Carrier::getById($this->carrierId);
        }

        return $this->carrier;
    }

    /**
     * @param $carrier
     * @throws \Exception
     */
    public function setCarrier($carrier)
    {
        if (!$carrier instanceof Carrier) {
            throw new \Exception("\$carrier must be instance of Carrier");
        }

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
        $this->carrierId = $carrierId;
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
