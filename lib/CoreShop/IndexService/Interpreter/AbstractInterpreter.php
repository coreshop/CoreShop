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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\IndexService\Interpreter;

use CoreShop\Exception\UnsupportedException;

/**
 * Class AbstractInterpreter
 * @package CoreShop\IndexService\Interpreter+
 */
class AbstractInterpreter
{
    /**
     * defined getters.
     *
     * @var array
     */
    protected static $interpreters = array('Object', 'Soundex', 'ObjectIdSum');

    /**
     * Add Interpreter Class.
     *
     * @param string $interpreter
     */
    public static function addInterpreter($interpreter)
    {
        if (!in_array($interpreter, self::$interpreters)) {
            self::$interpreters[] = $interpreter;
        }
    }

    /**
     * Get all Interpreter Classes.
     *
     * @return array
     */
    public static function getInterpreters()
    {
        return self::$interpreters;
    }

    /**
     * interpret value.
     *
     * @param mixed $value
     * @param array $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function interpret($value, $config = null)
    {
        throw new UnsupportedException('Not implemented in abstract');
    }
}
