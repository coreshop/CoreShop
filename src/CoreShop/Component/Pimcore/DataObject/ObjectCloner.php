<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
        $property = $reflection->getProperty('o_id');
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
