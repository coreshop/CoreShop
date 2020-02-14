<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;

final class ObjectCloner implements ObjectClonerInterface
{
    /**
     * {@inheritdoc}
     */
    public function cloneObject(Concrete $object, AbstractObject $parent, string $key): Concrete
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
