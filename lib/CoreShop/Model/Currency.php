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

class Currency extends AbstractModel {

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
    public $isoCode;

    /**
     * @var int
     */
    public $numericIsoCode;

    /**
     * @var string
     */
    public $symbol;

    /**
     * @var double
     */
    public $exchangeRate;

    /**
     * Save Currency
     *
     * @return mixed
     */
    public function save() {
        return $this->getResource()->save();
    }

    /**
     * Delete Currency
     *
     * @return mixed
     */
    public function delete() {
        return $this->getResource()->delete();
    }

    /**
     * Get Currency by name
     *
     * @param $name
     * @return Currency|null
     */
    public static function getByName($name) {
        try {
            $obj = new self;
            $obj->getResource()->getByName($name);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    /**
     * Get Currency by ID
     *
     * @param $id
     * @return Currency|null
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
     * Checks if currency is available
     *
     * @return array
     */
    public static function getAvailable()
    {
        $countries = Country::getActiveCountries();

        $currencies = array();

        foreach($countries as $c)
        {
            if(!array_key_exists($c->getCurrency()->getId(), $currencies))
                $currencies[$c->getCurrency()->getId()] = $c->getCurrency();
        }

        return array_values($currencies);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
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
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
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
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param mixed $numericIsoCode
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;
    }

    /**
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param mixed $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param mixed $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }
}