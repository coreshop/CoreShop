<?php

namespace CoreShop;

use CoreShop\Base;

use Pimcore\Model\Object;

class Order extends Base {
    
    public function importCart(Object\CoreShopCart $cart)
    {
        $items = array();
        $i = 1;
        
        foreach($cart->getItems() as $cartItem)
        {
            $item = new Object\CoreShopOrderItem();
            $item->setKey($i);
            $item->setParent(\CoreShop\Tool::findOrCreateObjectFolder($this->getFullPath() . "/items/"));
            $item->setPublished(true);
            
            $item->setProduct($cartItem->getProduct());
            $item->setWholesalePrice($cartItem->getProduct()->getWholesalePrice());
            $item->setRetailPrice($cartItem->getProduct()->getRetailPrice());
            $item->setTax($cartItem->getProduct()->getTax());
            $item->setPrice($cartItem->getProduct()->getPrice());
            $item->setAmount($cartItem->getAmount());
            $item->setExtraInformation($cartItem->getExtraInformation());
            $item->save();
            
            $items[] = $item;
            
            $i++;
        }
        
        $this->setItems($items);
        $this->save();
        
        return true;
    }
    
    public function createPayment(\CoreShop\Plugin\Payment $provider, $amount)
    {
        $payment = new Object\CoreShopPayment();
        $payment->setKey(uniqid());
        $payment->setPublished(true);
        $payment->setParent(\CoreShop\Tool::findOrCreateObjectFolder($this->getFullPath() . "/payments/"));
        $payment->setAmount($amount);
        $payment->setTransactionIdentifier(uniqid());
        $payment->setProvider($provider->getIdentifier());
        $payment->save();
        
        $this->addPayment($payment);
        
        return $payment;
    }
    
    public function addPayment(\Object\CoreShopPayment $payment)
    {
        $payments = $this->getPayments();
        
        if(!is_array($payments))
            $payments = array();
            
        $payments[] = $payment;
        
        $this->setPayments($payments);
        $this->save();
    }
    
    public function getTotal()
    {
        $total = 0;
        
        foreach($this->getItems() as $item)
        {
            $total += $item->getTotal();
        }
        
        return $total + $this->getDeliveryFee();
    }
}