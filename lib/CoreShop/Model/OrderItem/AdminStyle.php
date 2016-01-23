<?php

namespace CoreShop\Model\OrderItem;

use CoreShop\Model\OrderItem;
use Pimcore\Model\Object\AbstractObject;

class AdminStyle extends \Pimcore\Model\Element\AdminStyle {

    public function __construct($element) {
        parent::__construct($element);

        if($element instanceof OrderItem) {
            $this->elementIcon = '/pimcore/static/img/icon/page_white_copy.png';
            $this->elementIconClass = null;
        }
    }

}