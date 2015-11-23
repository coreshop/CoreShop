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

namespace CoreShop\Model\Listing;

use Pimcore\Model;

class AbstractListing extends Model\Listing\AbstractListing implements \Zend_Paginator_Adapter_Interface, \Zend_Paginator_AdapterAggregate, \Iterator {

    /**
     * List of PriceRule
     *
     * @var array
     */
    public $data = null;

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
    public function getData() {
        if ($this->data === null) {
            $this->load();
        }
        return $this->data;
    }

    /**
     * @param array $carriers
     * @return void
     */
    public function setData($data) {
        $this->data = $data;
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
        $this->getData();
        reset($this->data);
    }

    public function current() {
        $this->getData();
        $var = current($this->data);
        return $var;
    }

    public function key() {
        $this->getData();
        $var = key($this->data);
        return $var;
    }

    public function next() {
        $this->getData();
        $var = next($this->date);
        return $var;
    }

    public function valid() {
        $this->getData();
        $var = $this->current() !== false;
        return $var;
    }
}
