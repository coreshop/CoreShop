<?php

namespace CoreShop\Plugin;

interface Payment extends AbstractPlugin
{
    public function getPaymentFee(Pimcore\Model\Object\CoreShopCart $cart);
    public function processPayment(Pimcore\Model\Object\CoreShopOrder $order);
}