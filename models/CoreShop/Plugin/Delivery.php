<?php
    
namespace CoreShop\Plugin;

interface Delivery extends AbstractPlugin
{
    public function getDeliveryFee(Pimcore\Model\Object\CoreShopCart $cart);
}