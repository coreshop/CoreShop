<?php declare(strict_types=1);


namespace CoreShop\Component\Order\Factory;


use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUser;
use CoreShop\Component\Resource\Factory\FactoryInterface;

class CartPriceRuleVoucherCodeUserFactory implements CartPriceRuleVoucherCodeUserFactoryInterface
{

    public function __construct(
        private FactoryInterface $voucherCodePerUserFactory,
    ) {
    }

    public function createNew()
    {
        return $this->voucherCodePerUserFactory->createNew();
    }

    public function createWithInitialData(CustomerInterface $customer, CartPriceRuleVoucherCodeInterface $voucherCode): CartPriceRuleVoucherCodeUser
    {
        $voucherCodeUser = $this->createNew();
        $voucherCodeUser->setUserId($customer->getId());
        $voucherCodeUser->setUses(1);
        $voucherCodeUser->setVoucherCode($voucherCode);

        return $voucherCodeUser;
    }

}
