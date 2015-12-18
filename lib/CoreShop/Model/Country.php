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

namespace CoreShop\Model;

class Country extends AbstractModel {

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $isoCode;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $active;

    /**
     * @var Currency
     */
    public $currency;

    /**
     * @var int
     */
    public $currencyId;

    /**
     * @var int
     */
    public $zoneId;

    /**
     * @var Zone
     */
    public $zone;

    /**
     * save currency
     *
     * @return mixed
     */
    public function save() {
        return $this->getDao()->save();
    }

    /**
     * Get Currency by ID
     *
     * @param $id
     * @return Country|null
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
     * Get Currency by ISO-Code
     *
     * @param $isoCode
     * @return Country|null
     */
    public static function getByIsoCode($isoCode) {
        try {
            $obj = new self;
            $obj->getDao()->getByIsoCode($isoCode);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    /**
     * Gets all active Countries
     *
     * @return array
     */
    public static function getActiveCountries()
    {
        $list = new Country\Listing();
        $list->setCondition("active = 1");

        return $list->getData();
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
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param $isoCode
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param $active
     */
    public function setActive($active)
    {
        if(is_bool($active)) {
            if($active)
                $active = 1;
            else
                $active = 0;
        }
        $this->active = $active;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $currency
     * @throws \Exception
     */
    public function setCurrency($currency)
    {
        if(is_int($currency))
            $currency = Currency::getById($currency);

        if(!$currency instanceof Currency)
            throw new \Exception("\$currency must be instance of Currency");

        $this->currency = $currency;
        $this->currencyId = $currency->getId();
    }

    /**
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @param $currencyId
     * @throws \Exception
     */
    public function setCurrencyId($currencyId)
    {
        $currency = Currency::getById($currencyId);

        if(!$currency instanceof Currency)
            throw new \Exception("Currency with ID '$currencyId' not found");

        $this->currencyId = $currencyId;
        $this->currency = $currency;
    }

    /**
     * @return Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param $zone
     * @throws \Exception
     */
    public function setZone($zone)
    {
        if(is_int($zone))
            $zone = Zone::getById($zone);

        if(!$zone instanceof Zone)
            throw new \Exception("\$zone must be instance of Zone");

        $this->zone = $zone;
        $this->zoneId = $zone->getId();
    }

    /**
     * @return int
     */
    public function getZoneId()
    {
        return $this->zoneId;
    }

    /**
     * @param $zoneId
     * @throws \Exception
     */
    public function setZoneId($zoneId)
    {
        $zone = Zone::getById($zoneId);

        if(!$zone instanceof Zone) {
            $this->zoneId = null;
            $this->zone = null;
        }
        else {
            $this->zoneId = $zoneId;
            $this->zone = $zone;
        }
    }

    /**
     * @return string
     */
    public function __toString() {
        return strval($this->getName());
    }
}