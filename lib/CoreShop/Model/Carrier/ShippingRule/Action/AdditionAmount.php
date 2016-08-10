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

namespace CoreShop\Model\Carrier\ShippingRule\Action;

use CoreShop\Model\Cart;
use CoreShop\Model;

/**
 * Class AdditionAmount
 * @package CoreShop\Model\Carrier\ShippingRule\Action
 */
class AdditionAmount extends DiscountAmount
{
    /**
     * @var string
     */
    public $type = 'additionAmount';


    /**
     * get addition/discount for shipping
     *
     * @param Cart $cart
     * @param Model\User\Address $address
     * @param float $price
     *
     * @return float
     */
    public function getPriceModification(Cart $cart, Model\User\Address $address, $price)
    {
        return -1 * parent::getPriceModification($cart, $address, $price);
    }
}
