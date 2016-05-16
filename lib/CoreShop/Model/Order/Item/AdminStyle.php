<?php

namespace CoreShop\Model\Order\Item;

use CoreShop\Model\Order\Item;

class AdminStyle extends \Pimcore\Model\Element\AdminStyle
{
    /**
     * AdminStyle constructor.
     *
     * @param $element
     */
    public function __construct($element)
    {
        parent::__construct($element);

        if ($element instanceof Item) {
            $this->elementIcon = '/pimcore/static/img/icon/page_white_copy.png';
            $this->elementIconClass = null;
        }
    }
}
