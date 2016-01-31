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
use CoreShop\Model\PriceRule\AbstractPriceRule;

abstract class AbstractAction extends AbstractPriceRule
{
    /**
     * @var string
     */
    public $elementType = "action";

    /**
     * Apply Rule to Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    abstract public function applyRule(Model\Object\CoreShopCart $cart);

    /**
     * Remove Rule from Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    abstract public function unApplyRule(Model\Object\CoreShopCart $cart);

    /**
     * Calculate discount
     *
     * @param Model\Object\CoreShopCart $cart
     * @return int
     */
    abstract public function getDiscount(Model\Object\CoreShopCart $cart);
}
