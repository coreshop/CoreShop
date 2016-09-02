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

namespace CoreShop;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Cart;
use CoreShop\Model\Configuration;
use CoreShop\Model\Product;
use CoreShop\Model\Shop;
use Pimcore\Date;
use Pimcore\Model\Object;
use CoreShop\Model\Currency;
use CoreShop\Model\Country;
use CoreShop\Model\User;
use Pimcore\Tool\Session;
use GeoIp2\Database\Reader;

/**
 * Class Tool
 * @package CoreShop
 *
 * @deprecated please use \CoreShop::getTools(), will be removed in 1.2
 */
class Tool
{
    /**
     * @param $name
     * @param $arguments
     * @return bool
     *
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        $toolClass = \CoreShop::getTools();

        if (method_exists($toolClass, $name)) {
            return call_user_func_array(array($toolClass, $name), $arguments);
        }

        throw new Exception("Static Tool Method not found $name");
    }
}
