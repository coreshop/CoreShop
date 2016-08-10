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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;

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
     * @return []
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

        foreach ($this->getProducts() as $productId) {
            $pr = ProductModel::getById($productId);

            if ($pr instanceof ProductModel) {
                $found = true;
                break;
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
     * @param ProductModel    $product
     * @param ProductModel\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
    {
        return in_array($product->getId(), $this->getProducts());
    }
}
