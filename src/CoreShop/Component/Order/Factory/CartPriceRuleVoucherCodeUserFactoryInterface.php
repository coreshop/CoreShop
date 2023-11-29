<?php declare(strict_types=1);


namespace CoreShop\Component\Order\Factory;


use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUser;
use CoreShop\Component\Resource\Factory\FactoryInterface;

interface CartPriceRuleVoucherCodeUserFactoryInterface extends FactoryInterface
{
    public function createWithInitialData(CustomerInterface $customer, CartPriceRuleVoucherCodeInterface $voucherCode): CartPriceRuleVoucherCodeUser;
}

