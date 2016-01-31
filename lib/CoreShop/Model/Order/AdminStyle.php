<?php

namespace CoreShop\Model\Order;

use CoreShop\Model\Order;
use Pimcore\Model\Object\AbstractObject;

class AdminStyle extends \Pimcore\Model\Element\AdminStyle
{

    public function __construct($element)
    {
        parent::__construct($element);

        if ($element instanceof Order) {
            $this->elementIcon = '/pimcore/static/img/icon/page_white.png';
            $this->elementIconClass = null;
        }
    }
}
