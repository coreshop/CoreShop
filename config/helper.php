<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

if (!function_exists("recurse_copy")) {
    /**
     * Recursive copy entire Directory
     *
     * @param string $src
     * @param string $dst
     * @param boolean $overwrite
     */
    function recurse_copy($src, $dst, $overwrite = false)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    if (is_file($dst . "/" . $file) && $overwrite) {
                        if ($overwrite) {
                            unlink($dst . "/" . $file);
                            copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
    }
}

if (!function_exists("startsWith")) {
    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}

if (!function_exists("endsWith")) {
    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }
}

if (!function_exists("objectToArray")) {
    /**
     * Re-usable helper method.
     *
     * @todo move to the library helpers
     *
     * @static
     *
     * @param $object
     * @param null $fieldDefintions
     *
     * @return array
     */
    function _objectToArray($object, $fieldDefintions = null)
    {
        //if the given object is an array then loop through each element
        if (is_array($object)) {
            $collections = array();
            foreach ($object as $o) {
                $collections[] = $this->_objectToArray($o, $fieldDefintions);
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

        $collection = array();
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
                            $collection[$fieldName] = $this->_objectToArray($value->getItems(), $def['children']->getFieldDefinitions());
                        }
                    }
                    break;

                case 'date':
                    /* @var $value \Pimcore\Date */
                    $collection[$fieldName] = ($value instanceof \Pimcore\Date) ? $value->getTimestamp() : 0;
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
