<?php
    
namespace CoreShop\Plugin;

interface Shipping extends AbstractPlugin
{
    public function getShipping(\Pimcore\Model\Object\CoreShopCart $cart);
}