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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;

/**
 * Class Category
 * @package CoreShop\Model\PriceRule\Condition
 */
class Category extends AbstractCondition
{
    /**
     * @var int
     */
    public $category;

    /**
     * @var string
     */
    public $type = 'category';

    /**
     * @return \CoreShop\Model\Category
     */
    public function getCategory()
    {
        if (!$this->category instanceof \CoreShop\Model\Category) {
            $this->category = \CoreShop\Model\Category::getById($this->category);
        }

        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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

        if ($this->getCategory() instanceof \CoreShop\Model\Category) {
            foreach ($cart->getItems() as $i) {
                if ($i->getProduct()->inCategory($this->getCategory())) {
                    $found = true;
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
        return $this->getCategory() instanceof \CoreShop\Model\Category ? $product->inCategory($this->getCategory()) : false;
    }
}
