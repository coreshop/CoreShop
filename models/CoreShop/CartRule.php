<?php

namespace CoreShop;

use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;
use Pimcore\Model\Object\CoreShopCartRule;
use Pimcore\Model\Object\CoreShopProduct;

class CartRule extends Base
{
    public static function getHighlightItems()
    {
        $cartRules = new CoreShopCartRule\Listing();
        $cartRules->setCondition("(code IS NOT NULL AND code <> '') AND highlight = 1");
        $cartRules->setOrderKey("priority");
        $cartRules->setOrder("DESC");

        $cartRules = $cartRules->getObjects();

        $availableCartRules = array();

        foreach($cartRules as $cartRule)
        {
            if($cartRule->checkValidity(false, true))
            {
                $availableCartRules[] = $cartRule;
            }
        }

        return $availableCartRules;
    }

    public static function autoRemoveFromCart($cart = null)
    {
        if($cart == null)
            $cart = Tool::prepareCart();

        if($cart->getCartRule() instanceof CoreShopCartRule) {
            if ($cart->getCartRule()->checkValidity(false, true)) {
                die($cart->getCartRule());
                $cart->removeCartRule();
            }
        }
    }

    public static function autoAddToCart($cart = null)
    {
        if($cart == null)
            $cart = Tool::prepareCart();

        $cartRules = new CoreShopCartRule\Listing();
        $cartRules->setCondition("code IS NULL OR code = ''");
        $cartRules->setOrderKey("priority");
        $cartRules->setOrder("DESC");

        $cartRules = $cartRules->getObjects();

        foreach($cartRules as $cartRule)
        {
            if($cartRule->checkValidity(true)) {
                $cart->addCartRule($cartRule);
            }
        }

        return true;
    }

    public function getDiscount()
    {
        $cart = Tool::prepareCart();
        $discount = 0;

        if($this->getFreeShipping())
            $discount += $cart->getShipping();

        //Discount Type Percent applies on whole cart
        if($this->getDiscountType() == "percent")
        {
            $discount += $cart->getSubtotal() * $this->getDiscountPercent();
        }
        else if($this->getDiscountType() == "amount")
        {
            $discount += Tool::convertToCurrency($this->getDiscountAmount(), $this->getDiscountAmountCurrency(), Tool::getCurrency());
        }

        if($this->getFreeGift() instanceof CoreShopProduct)
        {
            $discount += $this->getFreeGift()->getProductPrice();
        }

        return $discount;
    }

    public function checkValidity($throwException = false, $alreadyInCart = false) {
        $session = Tool::getSession();
        $cart = Tool::prepareCart();

        //Check Availability
        $date = \Zend_Date::now();

        if($this->getFrom() instanceof \Zend_Date) {
            if (!$date->get(\Zend_Date::TIMESTAMP) < $this->getFrom()->get(\Zend_Date::TIMESTAMP)) {
                if($throwException) throw new \Exception("This voucher has expired"); else return false;
            }
        }

        if($this->getTo() instanceof \Zend_Date) {
            if (!$date->get(\Zend_Date::TIMESTAMP) > $this->getTo()->get(\Zend_Date::TIMESTAMP)) {
                if($throwException) throw new \Exception("This voucher has expired"); else return false;
            }
        }

        //Check Total Available
        if($this->getTotalAvailable() > 0)
            if($this->getTotalUsed() >= $this->getTotalAvailable())
                if($throwException) throw new \Exception("This voucher has already been used"); else return false;

        //Check Total For Customer
        if($session->user instanceof CoreShopUser)
        {
            $orders = $session->user->getOrders();
            $cartRulesUsed = 0;

            foreach($orders as $order)
            {
                if($order->getCartRule() instanceof CoreShopCartRule && $order->getCartRule()->getId() == $this->getId())
                    $cartRulesUsed++;
            }

            if($cartRulesUsed >= $this->getTotalPerCustomer())
                if($throwException) throw new \Exception("You cannot use this voucher anymore (usage limit reached)"); else return false;
        }

        //Check for Customer
        if($this->getCustomer() instanceof CoreShopUser && $session->user instanceof CoreShopUser)
        {
            if (!$this->getCustomer()->getId() == $session->user->getId())
            {
                if($throwException) throw new \Exception("You cannot use this voucher"); else return false;
            }
        }

        //Check Cart Amount
        if($this->getMinAmount() > 0)
        {
            $minAmount = $this->getMinAmount();
            $minAmount = Tools::convertToCurrency($minAmount, $this->getMinAmountCurrency(), Tool::getCurrency());

            $cartTotal = $cart->getTotal();

            if($minAmount > $cartTotal)
                if($throwException) throw new \Exception("You have not reached the minimum amount required to use this voucher"); else return false;
        }

        //Check Countries
        if(count($this->getCountries()) > 0)
        {
            if(!Tool::objectInList(Tool::getCountry(), $this->getCountries()))
            {
                if($throwException) throw new \Exception("You cannot use this voucher in your country of delivery"); else return false;
            }
        }

        //Check Products
        if(count($this->getProducts()) > 0)
        {
            $found = false;

            foreach($cart->getItems() as $i)
            {
                if(Tool::objectInList($i->getProduct(), $this->getProducts()))
                    $found = true;
            }

            if(!$found)
                if($throwException) throw new \Exception("You cannot use this voucher with these products"); else return false;
        }

        /* This loop checks:
            - if the voucher is already in the cart
            - if there are products in the cart (gifts excluded)
            Important note: this MUST be the last check, because if the tested cart rule has priority over a non combinable one in the cart, we will switch them
        */

        $products = $cart->getItems();

        if(count($products) > 0)
        {
            //TODO: Combine Vouchers

            if($cart->getCartRule() instanceof CoreShopCartRule)
            {
                if($this->getPriority() > $cart->getCartRule()->getPriority())
                    if($throwException) throw new \Exception("There is already a Rule in your Cart"); else return false;
            }
        }
        else
        {
            if($throwException) throw new \Exception("Cart is empty"); else return false;
        }

        $otherCartRule = $cart->getCartRule();

        if($otherCartRule instanceof CoreShopCartRule) {
            if ($otherCartRule->getId() == $this->getId() && $alreadyInCart) {
                return false;
            }
        }

        return true;
    }
}