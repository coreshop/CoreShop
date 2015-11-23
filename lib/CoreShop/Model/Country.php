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
    public $currency__id;

    /**
     * save currency
     *
     * @return mixed
     */
    public function save() {
        return $this->getResource()->save();
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
            $obj->getResource()->getById($id);
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
            $obj->getResource()->getByIsoCode($isoCode);
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

        return $list->getCountries();
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
        $this->currency__id = $currency->getId();
    }

    /**
     * @return int
     */
    public function getCurrency__Id()
    {
        return $this->currency__id;
    }

    /**
     * @param $currency__id
     * @throws \Exception
     */
    public function setCurrency__Id($currency__id)
    {
        $currency = null;

        if(is_int($currency__id)) {
            $currency = Currency::getById($currency__id);

            if(!$currency instanceof Currency)
                throw new \Exception("Currency with ID '$currency__id' not found");
        }

        $this->currency__id = $currency__id;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function __toString() {
        return strval($this->getName());
    }
}