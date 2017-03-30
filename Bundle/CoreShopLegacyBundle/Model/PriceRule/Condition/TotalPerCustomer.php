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
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Item;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\User;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product as ProductModel;

/**
 * Class TotalPerCustomer
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Condition
 */
class TotalPerCustomer extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'totalPerCustomer';

    /**
     * @var int
     */
    public $total;

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
        $user = \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getUser();

        //Check Total For Customer
        if ($user instanceof User) {
            $orders = $user->getOrders();
            $priceRulesUsed = 0;

            foreach ($orders as $order) {
                if ($order instanceof Order) {
                    foreach ($order->getPriceRuleFieldCollection() as $item) {
                        if ($item instanceof Item) {
                            if ($item->getVoucherCode()) {
                                $voucher = PriceRule\VoucherCode::getByCode($item->getVoucherCode());

                                if ($voucher instanceof PriceRule\VoucherCode) {
                                    ++$priceRulesUsed;
                                }
                            }
                        }
                    }
                }
            }

            if ($priceRule->getUseMultipleVoucherCodes()) {
                $isValid = $priceRulesUsed < $priceRule->getUsagePerVoucherCode();
            } else {
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
