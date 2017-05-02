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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Configuration;
use CoreShop\Model\Shop as ShopModel;
use CoreShop\Model\Product as ProductModel;

/**
 * Class Shops
 * @package CoreShop\Model\PriceRule\Condition
 */
class Shops extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'shops';

    /**
     * @var int[]
     */
    public $shops;

    /**
     * @return int[]
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * @param int[] $shops
     */
    public function setShops($shops)
    {
        $this->shops = $shops;
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
        return $this->check($throwException);
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
        return $this->check();
    }

    /**
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    protected function check($throwException = false)
    {
        if (Configuration::multiShopEnabled()) {
            $currentShop = ShopModel::getShop();
            $found = false;

            foreach ($this->getShops() as $shop) {
                if ($shop == $currentShop->getId()) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                if ($throwException) {
                    throw new Exception('You cannot use this voucher in this shop');
                }

                return false;
            }

            return true;
        }

        return false;
    }
}
