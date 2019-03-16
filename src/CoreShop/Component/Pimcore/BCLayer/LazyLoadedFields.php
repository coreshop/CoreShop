<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\BCLayer;

use Pimcore\Model\DataObject\LazyLoadedFieldsInterface;

class LazyLoadedFields
{
    public static function hasLazyKey($object, $key)
    {
        if (interface_exists(LazyLoadedFieldsInterface::class) && $object instanceof LazyLoadedFieldsInterface) {
            return $object->hasLazyKey($key);
        }

        if (method_exists($object, 'getO__loadedLazyFields')) {
            return in_array($key, $object->getO__loadedLazyFields());
        }

        throw new \InvalidArgumentException(
            sprintf(
            'Expected Object of Type "%s" to be either of interface LazyLoadedFieldsInterface or to have the method getO__loadedLazyFields',
                get_class($object)
            )
        );
    }

    public static function addLazyKey($object, $key)
    {
        if (interface_exists(LazyLoadedFieldsInterface::class) && $object instanceof LazyLoadedFieldsInterface) {
            $object->addLazyKey($key);
            return;
        }

        if (method_exists($object, 'addO__loadedLazyField')) {
            $object->addO__loadedLazyField($key);
            return;
        }

        throw new \InvalidArgumentException(
            sprintf(
            'Expected Object of Type "%s" to be either of interface LazyLoadedFieldsInterface or to have the method addO__loadedLazyField',
                get_class($object)
            )
        );
    }
}
