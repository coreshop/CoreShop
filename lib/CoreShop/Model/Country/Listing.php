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

namespace CoreShop\Model\Country;

use Pimcore\Model;

class Listing extends Model\Listing\AbstractListing implements \Zend_Paginator_Adapter_Interface, \Zend_Paginator_AdapterAggregate, \Iterator {

    /**
     * List of countries
     *
     * @var array
     */
    public $countries = null;

    /**
     * List of valid order keys
     *
     * @var array
     */
    public $validOrderKeys = array(
        "id",
        "country",
        "active",
        "currency__id"
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
    public function getCountries() {
        if ($this->countries === null) {
            $this->load();
        }
        return $this->countries;
    }

    /**
     * @param array $countries
     * @return void
     */
    public function setCountries($countries) {
        $this->countries = $countries;
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
        $this->getCountries();
        reset($this->countries);
    }

    public function current() {
        $this->getCountries();
        $var = current($this->countries);
        return $var;
    }

    public function key() {
        $this->getCountries();
        $var = key($this->countries);
        return $var;
    }

    public function next() {
        $this->getCountries();
        $var = next($this->countries);
        return $var;
    }

    public function valid() {
        $this->getCountries();
        $var = $this->current() !== false;
        return $var;
    }
}
