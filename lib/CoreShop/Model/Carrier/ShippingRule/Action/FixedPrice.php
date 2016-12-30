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
 * Class FixedPrice
 * @package CoreShop\Model\Carrier\ShippingRule\Action
 */
class FixedPrice extends AbstractAction
{
    /**
     * @var string
     */
    public static $type = 'fixedPrice';

    /**
     * @var float
     */
    public $fixedPrice;

    /**
     * get price for shipping
     *
     * @param Model\Carrier $carrier
     * @param Cart $cart
     * @param Model\User\Address $address
     *
     * @return float|boolean $price
     */
    public function getPrice(Model\Carrier $carrier, Cart $cart, Model\User\Address $address)
    {
        return $this->getFixedPrice();
    }

    /**
     * @return float
     */
    public function getFixedPrice()
    {
        return $this->fixedPrice;
    }

    /**
     * @param float $fixedPrice
     */
    public function setFixedPrice($fixedPrice)
    {
        $this->fixedPrice = $fixedPrice;
    }
}
