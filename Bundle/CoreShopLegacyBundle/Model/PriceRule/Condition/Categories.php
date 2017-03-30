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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Condition;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Category as CategoryModel;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product as ProductModel;

/**
 * Class Categories
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Condition
 */
class Categories extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'categories';

    /**
     * @var int[]
     */
    public $categories;

    /**
     * @return int[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param int[] $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Cart       $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return bool
     *
     * @throws Exception
     */
    public function checkConditionCart(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        $found = false;

        foreach ($this->getCategories() as $catId) {
            $cat = CategoryModel::getById($catId);

            if ($cat instanceof CategoryModel) {
                foreach ($cart->getItems() as $i) {
                    if ($i->getProduct()->inCategory($cat)) {
                        $found = true;
                    }
                }
            }
        }

        if (!$found) {
            if ($throwException) {
                throw new Exception('You cannot use this voucher with these products');
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param ProductModel $product
     * @param ProductModel\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
    {
        $found = false;

        foreach ($this->getCategories() as $catId) {
            $cat = CategoryModel::getById($catId);

            if ($cat instanceof CategoryModel) {
                if ($product->inCategory($cat)) {
                    $found = true;
                    break;
                }
            }
        }

        return $found;
    }
}
