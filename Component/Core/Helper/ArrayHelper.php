<?php

namespace CoreShop\Component\Core\Helper;

use Pimcore\Model\Object\AbstractObject;

class ArrayHelper {

    /**
     * @return array
     */
    public static function array_diff_assoc_recursive()
    {
        $args = func_get_args();
        $diff =  [ ];
        foreach (array_shift($args) as $key => $val) {
            for ($i = 0, $j = 0, $tmp =  [ $val ], $count = count($args); $i < $count; $i++) {
                if (is_array($val)) {
                    if (!isset($args[$i][$key]) || !is_array($args[$i][$key]) || empty($args[$i][$key])) {
                        $j++;
                    } else {
                        $tmp[] = $args[$i][$key];
                    }
                } elseif (! array_key_exists($key, $args[$i]) || $args[$i][$key] !== $val) {
                    $j++;
                }
            }
            if (is_array($val)) {
                $tmp = call_user_func_array(array(__CLASS__, __FUNCTION__), $tmp);
                if (! empty($tmp)) {
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
     * @param AbstractObject $object
     * @param null $fieldDefintions
     * @return array|bool
     */
    public static function objectToArray(AbstractObject $object, $fieldDefintions = null)
    {
        //if the given object is an array then loop through each element
        if (is_array($object)) {
            $collections = [];
            foreach ($object as $o) {
                $collections[] = _objectToArray($o, $fieldDefintions);
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
                    if (($value instanceof \Pimcore\Model\Object\Fieldcollection) && is_array($value->getItems())) {
                        /* @var $value \Pimcore\Model\Object\Fieldcollection */
                        $def = $value->getItemDefinitions();
                        if (method_exists($def['children'], 'getFieldDefinitions')) {
                            $collection[$fieldName] = _objectToArray($value->getItems(), $def['children']->getFieldDefinitions());
                        }
                    }
                    break;

                case 'date':
                    /* @var $value \Pimcore\Date */
                    $collection[$fieldName] = ($value instanceof \Pimcore\Date || $value instanceof \Carbon\Carbon) ? $value->getTimestamp() : 0;
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