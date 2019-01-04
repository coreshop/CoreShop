<?php

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;

interface ObjectClonerInterface
{
    /**
     * Clones an object and returns it unsaved.
     *
     * @param Concrete $object
     * @param Concrete $parent
     * @param string   $key
     *
     * @return Concrete
     */
    public function cloneObject(Concrete $object, $parent, $key);
}
