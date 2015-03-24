<?php
    
interface CoreShop_Interface_Delivery extends CoreShop_Interface_Plugin
{
    public function getDeliveryFee(Object_CoreShopCart $cart);
}