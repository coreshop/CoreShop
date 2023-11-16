<?php declare(strict_types=1);


namespace CoreShop\Component\Order\Model;


use CoreShop\Component\Resource\Model\ResourceInterface;

interface CartPriceRuleVoucherCodeUserInterface extends ResourceInterface
{
    public function getVoucherCode(): CartPriceRuleVoucherCodeInterface;

    public function getUses(): int;

    public function incrementUses(): void;

    public function getUserId(): int;
}
