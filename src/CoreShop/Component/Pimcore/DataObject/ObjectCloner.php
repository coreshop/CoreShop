<?php

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;

final class ObjectCloner implements ObjectClonerInterface
{
    /**
     * {@inheritdoc}
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

\class_alias(ObjectCloner::class, 'CoreShop\Component\Resource\Pimcore\ObjectCloner');