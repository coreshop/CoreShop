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

namespace CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Getter;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception\UnsupportedException;
use CoreShop\Bundle\CoreShopLegacyBundle\IndexService;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;

/**
 * Class AbstractGetter
 * @package CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Getter
 */
class AbstractGetter
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
     * Add Getter Class.
     *
     * @param string $getter
     *
     * @deprecated will be removed with version 1.3
     */
    public static function addGetter($getter)
    {
        IndexService::getGetterDispatcher()->addType('\CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Getter\\' . $getter);
    }

    /**
     * Get all Getter Classes.
     *
     * @return array
     *
     * @deprecated will be removed with version 1.3
     */
    public static function getGetters()
    {
        return IndexService::getGetterDispatcher()->getTypeKeys();
    }

    /**
     * get value.
     *
     * @param $object
     * @param Column $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function get(Product $object, Column $config)
    {
        throw new UnsupportedException('Not implemented in abstract');
    }
}
