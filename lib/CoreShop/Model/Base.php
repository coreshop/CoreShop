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

use CoreShop\Exception;
use CoreShop\Tool;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\Concrete;

class Base extends Concrete
{
    /**
     * Pimcore Object Class
     *
     * @var string
     */
    public static $pimcoreClass = null;

    /**
     * get Pimcore implementation class
     *
     * @throws Exception
     * @return string
     */
    public static function getPimcoreObjectClass() {
        return static::$pimcoreClass;
    }

    /**
     * Create new instance of Pimcore Object
     *
     * @throws Exception
     * @return static
     */
    public static function create() {
        $pimcoreClass = self::getPimcoreObjectClass();

        if(\Pimcore\Tool::classExists($pimcoreClass)) {
            return new $pimcoreClass();
        }

        throw new Exception("Class $pimcoreClass not found");
    }

    /**
     * returns the class ID of the current object class
     * @return int
     */
    public static function classId()
    {
        $v = get_class_vars(self::getPimcoreObjectClass());
        return $v["o_classId"];
    }
    
    /**
     * Object to Array
     * 
     * @return array
     */
    public function toArray()
    {
        return Tool::objectToArray($this);
    }

    /**
     * Admin Element Style
     *
     * @return \Pimcore\Model\Element\AdminStyle
     */
    public function getElementAdminStyle()
    {
        if (!$this->o_elementAdminStyle) {
            $class = get_parent_class(get_called_class());
            $class .= "\\AdminStyle";

            if (\Pimcore\Tool::classExists($class)) {
                $this->o_elementAdminStyle = new $class($this);
            } else {
                $this->o_elementAdminStyle = parent::getElementAdminStyle();
            }
        }

        return $this->o_elementAdminStyle;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed|null
     * @throws \Exception
     */
    public static function __callStatic($method, $arguments)
    {
        $pimcoreClass = self::getPimcoreObjectClass();

        if(get_called_class() === $pimcoreClass) {
            return parent::__callStatic($method, $arguments);
        }
        
        if(!\Pimcore\Tool::classExists($pimcoreClass)) {
            throw new Exception("Calling to unkown class " . $pimcoreClass);
        }

        return call_user_func_array(array($pimcoreClass, $method), $arguments);
    }
}
