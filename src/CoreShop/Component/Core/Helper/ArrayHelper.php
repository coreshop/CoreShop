<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Helper;

use Carbon\Carbon;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;

class ArrayHelper
{
    /**
     * @return array
     */
    public static function array_diff_assoc_recursive()
    {
        $args = func_get_args();
        $diff = [];
        foreach (array_shift($args) as $key => $val) {
            for ($i = 0, $j = 0, $tmp = [$val], $count = count($args); $i < $count; ++$i) {
                if (is_array($val)) {
                    if (!isset($args[$i][$key]) || !is_array($args[$i][$key]) || empty($args[$i][$key])) {
                        ++$j;
                    } else {
                        $tmp[] = $args[$i][$key];
                    }
                } elseif (!array_key_exists($key, $args[$i]) || $args[$i][$key] !== $val) {
                    ++$j;
                }
            }
            if (is_array($val)) {
                $tmp = call_user_func_array([__CLASS__, __FUNCTION__], $tmp);
                if (!empty($tmp)) {
                    $diff[$key] = $tmp;
                } elseif ($j == $count) {
                    $diff[$key] = $val;
                }
            } elseif ($j == $count && $count) {
                $diff[$key] = $val;
            }
        }

        return $diff;
    }

    /**
     * @param Concrete $object
     * @param null $fieldDefintions
     *
     * @return array|false
     */
    public static function objectToArray(Concrete $object, $fieldDefintions = null)
    {
        //if the given object is an array then loop through each element
        if (is_array($object)) {
            $collections = [];
            foreach ($object as $o) {
                $collections[] = static::objectToArray($o, $fieldDefintions);
            }

            return $collections;
        }
        if (!is_object($object)) {
            return false;
        }

        //Custom list field definitions
        if (null === $fieldDefintions) {
            $fieldDefintions = $object->getClass()->getFieldDefinitions();
        }

        $collection = [];
        foreach ($fieldDefintions as $fd) {
            $fieldName = $fd->getName();
            $getter = 'get'.ucfirst($fieldName);
            $value = $object->$getter();

            switch ($fd->getFieldtype()) {
                case 'fieldcollections':
                    if (($value instanceof Fieldcollection) && is_array($value->getItems())) {
                        /* @var $value Fieldcollection */
                        $def = $value->getItemDefinitions();
                        if (method_exists($def['children'], 'getFieldDefinitions')) {
                            $collection[$fieldName] = static::objectToArray($value->getItems(), $def['children']->getFieldDefinitions());
                        }
                    }
                    break;

                case 'date':
                    /* @var $value \Pimcore\Date */
                    $collection[$fieldName] = ($value instanceof Carbon) ? $value->getTimestamp() : 0;
                    break;
                default:
                    /* @var $value string */
                    $collection[$fieldName] = $value;
            }
        }

        //Parent class properties
        $collection['id'] = $object->o_id;
        $collection['key'] = $object->o_key;

        return $collection;
    }
}
