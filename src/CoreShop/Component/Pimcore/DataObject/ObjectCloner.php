<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
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
        $property->setAccessible(true);
        $property->setValue($newObject, null);
        $property->setAccessible(false);

        $newObject->setParent($parent);
        $newObject->setKey($key);

        if ($saveDirectly) {
            $newObject->save();
        }

        return $newObject;
    }
}
