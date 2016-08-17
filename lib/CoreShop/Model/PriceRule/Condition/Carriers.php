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
use CoreShop\Model\Carrier;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Tool;

/**
 * Class Carriers
 * @package CoreShop\Model\PriceRule\Condition
 */
class Carriers extends AbstractCondition
{
    /**
     * @var int[]
     */
    public $carriers;

    /**
     * @var string
     */
    public $type = 'carriers';

    /**
     * @return int[]
     */
    public function getCarriers()
    {
        return $this->carriers;
    }

    /**
     * @param int[] carriers
     */
    public function setCarriers($carriers)
    {
        $this->carriers = $carriers;
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
        return $this->check($cart);
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
        return $this->check(Tool::prepareCart());
    }

    /**
     * @param Cart $cart
     * @param bool $throwException
     * @return bool
     * @throws Exception
     * @throws \Exception
     */
    private function check(Cart $cart, $throwException = false)
    {
        if ($cart->getCarrier() instanceof Carrier) {
            if (in_array($cart->getCarrier()->getId(), $this->getCarriers())) {
                if ($throwException) {
                    throw new Exception('You cannot use this voucher.');
                }

                return true;
            }
        }

        return false;
    }
}
