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

namespace CoreShop\Model\PriceRule\Action;

use Pimcore\Model;

class FreeShipping extends AbstractAction
{
    /**
     * @var string
     */
    public $type = "freeShipping";

    /**
     * Calculate discount
     *
     * @param Model\Object\CoreShopCart $cart
     * @return int
     */
    public function getDiscount(Model\Object\CoreShopCart $cart)
    {
        return 0;
    }

    /**
     * Apply Rule to Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    public function applyRule(Model\Object\CoreShopCart $cart)
    {
        return true;
    }

    /**
     * Remove Rule from Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    public function unApplyRule(Model\Object\CoreShopCart $cart)
    {
        return true;
    }
}
