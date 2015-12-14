<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Model\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopUser;

class TotalPerCustomer extends AbstractCondition {

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
        if($session->user instanceof CoreShopUser)
        {
            $orders = $session->user->getOrders();
            $priceRulesUsed = 0;

            foreach($orders as $order)
            {
                if($order->getPriceRule() instanceof PriceRule && $order->getPriceRule()->getId() == $priceRule->getId())
                    $priceRulesUsed++;
            }

            if($priceRulesUsed >= $this->getTotal())
                if($throwException) throw new \Exception("You cannot use this voucher anymore (usage limit reached)"); else return false;
        }

        return true;
    }
}
