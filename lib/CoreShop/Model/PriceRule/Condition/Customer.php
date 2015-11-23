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

class Customer extends AbstractCondition {

    /**
     * @var int
     */
    public $customer;

    /**
     * @var string
     */
    public $type = "customer";

    /**
     * @return int
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param int $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
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

        if($cart->getUser() instanceof CoreShopUser && $session->user instanceof CoreShopUser)
        {
            if (!$cart->getUser()->getId() == $session->user->getId())
            {
                if($throwException) throw new \Exception("You cannot use this voucher"); else return false;
            }
        }

        return true;
    }
}
