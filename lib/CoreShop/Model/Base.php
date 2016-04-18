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
use Pimcore\Model\Object\Concrete;

class Base extends Concrete
{
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
}
