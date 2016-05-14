<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Cart\PriceRule\Condition;

use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\User;
use CoreShop\Tool;

class TotalPerCustomer extends AbstractCondition
{
    /**
     * @var int
     */
    public $total;

    /**
     * @var string
     */
    public $type = "totalPerCustomer";

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
     * Check if Cart is Valid for Condition
     *
     * @param Cart $cart
     * @param PriceRule $priceRule
     * @param bool|false $throwException
     * @return bool
     * @throws \Exception
     */
    public function checkCondition(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        $session = Tool::getSession();

        //Check Total For Customer
        if ($session->user instanceof User) {
            $orders = $session->user->getOrders();
            $priceRulesUsed = 0;

            foreach ($orders as $order) {
                if ($order->getPriceRule() instanceof PriceRule && $order->getPriceRule()->getId() == $priceRule->getId()) {
                    $priceRulesUsed++;
                }
            }

            if ($priceRulesUsed >= $this->getTotal()) {
                if ($throwException) {
                    throw new \Exception("You cannot use this voucher anymore (usage limit reached)");
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
