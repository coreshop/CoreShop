<?php

namespace CoreShop\Component\Resource\Pimcore;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;

final class ObjectCloner
{
    /**
     * Clones an object and returns it unsaved
     *
     * @param Concrete $object
     * @param $parent
     * @param $key
     *
     * @return Concrete
     */
    public function cloneObject(Concrete $object, $parent, $key)
    {
        Service::loadAllObjectFields($object);

        $newObject = clone $object;
        $newObject->setId(null);
        $newObject->setParent($parent);
        $newObject->setKey($key);
        $newObject->save();

        return $newObject;
    }
}