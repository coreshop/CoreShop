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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;
use Pimcore\Model\Object\Concrete;

/**
 * Class TotalPayed
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Order
 */
class TotalPayed
{
    /**
     * compute order total payed.
     *
     * @param $object Concrete
     * @param $context \Pimcore\Model\Object\Data\CalculatedValue
     *
     * @return string
     */
    public static function compute($object, $context)
    {
        if ($object instanceof Order) {
            return $object->getPayedTotal();
        }

        return 0;
    }
}
