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

use CoreShop\Tool;
use Pimcore\Model\Object;

/**
 * Class BrickVariant
 * @package CoreShop\Model
 */
class BrickVariant extends Object\Objectbrick\Data\AbstractData
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
    public function getValueForVariant( $fieldInfo = array(), $language = 'en' )
    {
        $methodName = 'get' . $fieldInfo['name'];

        if( !method_exists( $this, $methodName ) )
        {
            return $methodName . ' not implemented';
        }

        $output = $this->{$methodName}();
        $data = FALSE;

        if( is_string( $output ) )
        {
            $data = $output;
        }
        else if( is_float( $output ) )
        {
            $data = $output;
        }
        else if( is_int( $output ) )
        {
            $data = $output;
        }
        else if( is_bool( $output ) )
        {
            $data = $fieldInfo['name'];
        }
        else if( is_array( $output ) )
        {
            foreach ($output as $t)
            {
                if ($t->getType() == 'object')
                {
                    //check if there are some localize fields?
                    if (isset($t->localizedfields) && $t->localizedfields instanceof Object\LocalizedField)
                    {
                        $items = $t->getLocalizedFields()->getItems();

                        if (isset($items[$language]))
                        {
                            $itemValues = array_values($items[$language]);
                        }
                    }
                    else
                    {
                        $items = $t->getItems();
                        $itemValues = is_array($items) ? array_values($items[$language]) : array();
                    }

                    if (isset($itemValues[0]) && is_string($itemValues[0]))
                    {
                        $data = $itemValues[0];
                    }
                }
            }
        }

        return $data;

    }

}