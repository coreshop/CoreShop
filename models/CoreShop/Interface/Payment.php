<?php

namespace CoreShop\Interface;

interface Payment extends Plugin
{
    public function getPaymentFee(Object\CoreShopCart $cart);
    
    public function processPayment(Object\CoreShopOrder $order);
}