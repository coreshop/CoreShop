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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model\Object\Fieldcollection\Data;

use CoreShop\Exception;
use Pimcore\Tool;

class AbstractData extends \Pimcore\Model\Object\Fieldcollection\Data\AbstractData
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = null;

    /**
     * get Pimcore implementation class.
     *
     * @return string
     */
    public static function getPimcoreObjectClass()
    {
        $classFile = Tool::getModelClassMapping(get_called_class());

        return $classFile::$pimcoreClass;
    }

    /**
     * Create new instance of Pimcore Object.
     *
     * @throws Exception
     *
     * @return static
     */
    public static function create()
    {
        $pimcoreClass = self::getPimcoreObjectClass();

        if (Tool::classExists($pimcoreClass)) {
            return new $pimcoreClass();
        }

        throw new Exception("Class $pimcoreClass not found");
    }
}
