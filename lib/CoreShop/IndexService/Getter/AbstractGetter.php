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

namespace CoreShop\IndexService\Getter;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Product;

class AbstractGetter {

    /**
     * defined getters
     *
     * @var array
     */
    protected static $getter = array("Brick", "ClassificationStore");

    /**
     * @param string $getter
     */
    public static function addGetter($getter)
    {
        if (!in_array($getter, self::$getter)) {
            self::$getter[] = $getter;
        }
    }

    /**
     * @return array
     */
    public static function getGetter()
    {
        return self::$getter;
    }

    /**
     * @param $object
     * @param array $config
     * @return mixed
     * @throws UnsupportedException
     */
    public static function get(Product $object, $config = null) {
        throw new UnsupportedException("Not implemented in abstract");
    }
}
