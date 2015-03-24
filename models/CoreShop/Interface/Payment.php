<?php
    
interface CoreShop_Interface_Payment extends CoreShop_Interface_Plugin
{
    public function getPaymentFee(Object_CoreShopCart $cart);
    
    public function processPayment(Object_CoreShopOrder $order);
}