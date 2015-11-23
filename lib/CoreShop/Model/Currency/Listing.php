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

namespace CoreShop\Model\Currency;

use Pimcore\Model;

class Listing extends Model\Listing\AbstractListing implements \Zend_Paginator_Adapter_Interface, \Zend_Paginator_AdapterAggregate, \Iterator {

    /**
     * List of currencies
     *
     * @var array
     */
    public $currencies = null;

    /**
     * List of valid order keys
     *
     * @var array
     */
    public $validOrderKeys = array(
        "id",
        "name",
        "isoCode",
        "numericIsoCode",
        "exchangeRate",
        "symbol"
    );

    /**
     * Test if the passed key is valid
     *
     * @param string $key
     * @return boolean
     */
    public function isValidOrderKey($key) {
        return true;
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        if ($this->currencies === null) {
            $this->load();
        }
        return $this->currencies;
    }

    /**
     * @param string $currencies
     * @return void
     */
    public function setCurrencies($currencies) {
        $this->currencies = $currencies;
        return $this;
    }


    /**
     *
     * Methods for \Zend_Paginator_Adapter_Interface
     */

    public function count() {
        return $this->getTotalCount();
    }

    public function getItems($offset, $itemCountPerPage) {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);
        return $this->load();
    }

    public function getPaginatorAdapter() {
        return $this;
    }


    /**
     * Methods for Iterator
     */

    public function rewind() {
        $this->getCurrencies();
        reset($this->currencies);
    }

    public function current() {
        $this->getCurrencies();
        $var = current($this->currencies);
        return $var;
    }

    public function key() {
        $this->getCurrencies();
        $var = key($this->currencies);
        return $var;
    }

    public function next() {
        $this->getCurrencies();
        $var = next($this->currencies);
        return $var;
    }

    public function valid() {
        $this->getCurrencies();
        $var = $this->current() !== false;
        return $var;
    }
}
