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

namespace CoreShop\Model\PriceRule;

use Pimcore\Model;

class Listing extends Model\Listing\AbstractListing implements \Zend_Paginator_Adapter_Interface, \Zend_Paginator_AdapterAggregate, \Iterator {

    /**
     * List of PriceRule
     *
     * @var array
     */
    public $priceRules = null;

    /**
     * List of valid order keys
     *
     * @var array
     */
    public $validOrderKeys = array(
        "id",
        "active"
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
    public function getPriceRules() {
        if ($this->priceRules === null) {
            $this->load();
        }
        return $this->priceRules;
    }

    /**
     * @param array $priceRules
     * @return void
     */
    public function setPriceRules($priceRules) {
        $this->priceRules = $priceRules;
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
        $this->getPriceRules();
        reset($this->priceRules);
    }

    public function current() {
        $this->getPriceRules();
        $var = current($this->priceRules);
        return $var;
    }

    public function key() {
        $this->getPriceRules();
        $var = key($this->priceRules);
        return $var;
    }

    public function next() {
        $this->getPriceRules();
        $var = next($this->priceRules);
        return $var;
    }

    public function valid() {
        $this->getPriceRules();
        $var = $this->current() !== false;
        return $var;
    }
}
