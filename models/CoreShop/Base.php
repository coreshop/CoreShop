<?php

namespace CoreShop;

use Pimcore\Model\Object\Concrete;

class Base extends Concrete
{
    public function toArray()
    {
        return CoreShop\Tool::objectToArray($this);
    }
}
