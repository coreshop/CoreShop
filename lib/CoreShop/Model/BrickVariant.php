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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Model\Objectbrick\Data\Objectbrick;
use CoreShop\Tool;
use Pimcore\Model\Object;

/**
 * Class BrickVariant
 * @package CoreShop\Model
 */
class BrickVariant extends Objectbrick
{
    /**
     * Format Value from Brick Element.
     *
     * override this class / method to implement your own formatting logic.
     * if method returns FALSE no value will be added.
     *
     * @param array  $fieldInfo name,type,title
     * @param string $language
     *
     * @return bool|float|int|string
     */
    public function getValueForVariant($fieldInfo = array(), $language = 'en')
    {
        $methodName = 'get' . $fieldInfo['name'];

        if (!method_exists($this, $methodName)) {
            return $methodName . ' not implemented';
        }

        $output = $this->{$methodName}();
        $data = false;

        if (is_string($output)) {
            $data = $output;
        } elseif (is_float($output)) {
            $data = $output;
        } elseif (is_int($output)) {
            $data = $output;
        } elseif (is_bool($output)) {
            $data = $fieldInfo['name'];
        } elseif (is_array($output)) {
            //maybe the object contains multiple values. only use the first one.
            $object = $output[0];

            if ($object->getType() == 'object') {
                $data = $this->extractObjectData($object, $language);
            }
        } elseif (is_object($output)) {
            $data = $this->extractObjectData($output, $language);
        }

        return $data;
    }

    /**
     * Get Name for Variant Value
     *
     * @param $fieldInfo
     * @return mixed
     */
    public function getNameForVariant($fieldInfo)
    {
        return $fieldInfo['name'];
    }

    private function extractObjectData($object, $language)
    {
        $data = false;

        //check if there are some localize fields?
        if (isset($object->localizedfields) && $object->localizedfields instanceof Object\LocalizedField) {
            $items = $object->getLocalizedFields()->getItems();

            if (isset($items[$language])) {
                $itemValues = array_values($items[$language]);
            }
        } else {
            $items = $object->getItems();
            $itemValues = is_array($items) ? array_values($items[$language]) : array();
        }

        if (isset($itemValues[0]) && is_string($itemValues[0])) {
            $data = $itemValues[0];
        }

        return $data;
    }
}
