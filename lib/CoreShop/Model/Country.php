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

    public $id;
    public $isoCode;
    public $name;
    public $active;
    public $currency;
    public $currency__id;

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

    public static function getActiveCountries()
    {
        $list = new Country\Listing();
        $list->setCondition("active = 1");

        return $list->getCountries();
    }

    /**
 * @return mixed
 */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param mixed $isoCode
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
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
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
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
     * @return mixed
     */
    public function getCurrency__Id()
    {
        return $this->currency__id;
    }

    /**
     * @param mixed $currency__id
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

    public function __toString() {
        return strval($this->getName());
    }
}