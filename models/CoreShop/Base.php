<?php
    
namespace CoreShop;

use Pimcore\Model\Object;

class Base extends Object\Concrete
{
    public function toArray()
    {
        return CoreShop\Tool::objectToArray($this);
    }
}
