<?php
    
namespace CoreShop\Interface;

interface Delivery extends Plugin
{
    public function getDeliveryFee(Object_CoreShopCart $cart);
}