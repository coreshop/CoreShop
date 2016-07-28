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
use CoreShop\Model\User;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Tool;

/**
 * Class TotalPerCustomer
 * @package CoreShop\Model\PriceRule\Condition
 */
class TotalPerCustomer extends AbstractCondition
{
    /**
     * @var int
     */
    public $total;

    /**
     * @var string
     */
    public $type = 'totalPerCustomer';

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
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
        $user = Tool::getUser();

        //Check Total For Customer
        if ($user instanceof User) {
            $orders = $user->getOrders();
            $priceRulesUsed = 0;

            foreach ($orders as $order) {
                if ($order->getPriceRule() instanceof PriceRule && $order->getPriceRule()->getId() == $priceRule->getId()) {
                    if($priceRule->getUseMultipleVoucherCodes()) {
                        if($cart->getVoucher()) {
                            $voucher = PriceRule\VoucherCode::getByCode($cart->getVoucher());

                            if($voucher instanceof PriceRule\VoucherCode) {
                                ++$priceRulesUsed;
                            }
                        }
                    }
                    else {
                        ++$priceRulesUsed;
                    }
                }
            }

            $isValid = true;

            if($priceRule->getUseMultipleVoucherCodes()) {
                $isValid = $priceRulesUsed < $priceRule->getUsagePerVoucherCode();
            }
            else {
                $isValid = !($priceRulesUsed >= $this->getTotal());
            }

            if (!$isValid) {
                if ($throwException) {
                    throw new Exception('You cannot use this voucher anymore (usage limit reached)');
                } else {
                    return false;
                }
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
        return false;
    }
}
