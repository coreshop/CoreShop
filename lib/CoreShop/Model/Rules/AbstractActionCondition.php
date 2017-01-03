<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Rules;

/**
 * Class AbstractActionCondition
 * @package CoreShop\Model\Rules
 */
class AbstractActionCondition
{
    /**
     * @var string
     */
    public static $elementType;

    /**
     * @var string
     */
    public static $type;

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }

            $setter = 'set'.ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getElementType(), $this->getType());
    }

    /**
     * @return string
     */
    public static function getElementType()
    {
        return static::$elementType;
    }

    /**
     * @return string
     */
    public static function getType()
    {
        return static::$type;
    }
}
