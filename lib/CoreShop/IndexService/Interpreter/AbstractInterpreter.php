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

namespace CoreShop\IndexService\Interpreter;

use CoreShop\Exception\UnsupportedException;
use CoreShop\IndexService;
use CoreShop\Model\Index\Config\Column;
use CoreShop\Model\Index\Config\Column\AbstractColumn;

/**
 * Class AbstractInterpreter
 * @package CoreShop\IndexService\Interpreter+
 */
class AbstractInterpreter
{
    /**
     * @var string
     */
    public static $type = null;

    /**
     * @return string
     */
    public static function getType()
    {
        return static::$type;
    }

    /**
     * Add Interpreter Class.
     *
     * @param string $interpreter
     *
     * @deprecated will be removed with version 1.3
     */
    public static function addInterpreter($interpreter)
    {
        IndexService::getInterpreterDispatcher()->addType('\CoreShop\IndexService\Interpreter\\' . $interpreter);
    }

    /**
     * Get all Interpreter Classes.
     *
     * @return array
     *
     * @deprecated will be removed with version 1.3
     */
    public static function getInterpreters()
    {
        return IndexService::getInterpreterDispatcher()->getTypeKeys();
    }

    /**
     * interpret value.
     *
     * @param mixed $value
     * @param Column $config
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
