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
use CoreShop\Model\Category;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\User\Address;

/**
 * Class Categories
 * @package CoreShop\Model\PriceRule\Condition
 */
class Categories extends AbstractCondition
{
    /**
     * @var int[]
     */
    public $categories;

    /**
     * @var string
     */
    public $type = 'categories';

    /**
     * @return int[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param int $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
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
        $found = false;

        foreach ($cart->getItems() as $item) {
            foreach ($this->getCategories() as $catId) {
                $cat = Category::getById($catId);

                if ($cat instanceof Category) {
                    if ($item->getProduct()->inCategory($cat)) {
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                return true;
            }
        }

        return false;
    }
}
