<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;

final class ObjectCloner implements ObjectClonerInterface
{
    public function cloneObject(Concrete $object, AbstractObject $parent, string $key, bool $saveDirectly = true): Concrete
    {
        Service::loadAllObjectFields($object);

        $newObject = clone $object;
        $reflection = new \ReflectionClass($newObject);
        $property = $reflection->getProperty('id');
        $property->setValue($newObject, null);

        $newObject->setParent($parent);
        $newObject->setKey($key);

        if ($saveDirectly) {
            $newObject->save();
        }

        return $newObject;
    }
}
