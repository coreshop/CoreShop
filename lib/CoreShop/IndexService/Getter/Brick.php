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

class Brick extends AbstractGetter {

    /**
     * @param $object
     * @param array $config
     * @return mixed
     * @throws UnsupportedException
     */
    public static function get(Product $object, $config = null) {
        $brickContainerGetter = "get" . ucfirst($config['config']['brickfield']);
        $brickContainer = $object->$brickContainerGetter();

        $key = explode("~", $config['key']);

        $bricktype = $key[0];
        $key = $key[1];

        $brickGetter = "get" . ucfirst($bricktype);
        $brick = $brickContainer->$brickGetter();

        if($brick) {
            $fieldGetter = "get" . ucfirst($key);
            return $brick->$fieldGetter();
        }

        return null;
    }
}
