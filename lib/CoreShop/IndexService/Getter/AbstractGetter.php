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

namespace CoreShop\IndexService\Getter;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Index\Config\Column\AbstractColumn;
use CoreShop\Model\Product;

/**
 * Class AbstractGetter
 * @package CoreShop\IndexService\Getter
 */
class AbstractGetter
{
    /**
     * defined getters.
     *
     * @var array
     */
    protected static $getter = array('Brick', 'Classificationstore', 'Localizedfield', 'Fieldcollection');

    /**
     * Add Getter Class.
     *
     * @param string $getter
     */
    public static function addGetter($getter)
    {
        if (!in_array($getter, self::$getter)) {
            self::$getter[] = $getter;
        }
    }

    /**
     * Get all Getter Classes.
     *
     * @return array
     */
    public static function getGetters()
    {
        return self::$getter;
    }

    /**
     * get value.
     *
     * @param $object
     * @param AbstractColumn $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function get(Product $object, AbstractColumn $config)
    {
        throw new UnsupportedException('Not implemented in abstract');
    }
}
