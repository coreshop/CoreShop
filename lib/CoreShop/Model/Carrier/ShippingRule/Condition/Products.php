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

namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model\Carrier;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\User\Address;

/**
 * Class Products
 * @package CoreShop\Model\PriceRule\Condition
 */
class Products extends AbstractCondition
{
    /**
     * @var int[]
     */
    public $products;

    /**
     * @var string
     */
    public $type = 'products';

    /**
     * @return int[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param int[] $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Carrier $carrier
     * @param Cart $cart
     * @param Address $address;
     * @param ShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Carrier $carrier, Cart $cart, Address $address, ShippingRule $shippingRule)
    {
        foreach ($cart->getItems() as $item) {
            $found = in_array($item->getProduct()->getId(), $this->getProducts());

            if ($found) {
                return true;
            }
        }

        return false;
    }
}
