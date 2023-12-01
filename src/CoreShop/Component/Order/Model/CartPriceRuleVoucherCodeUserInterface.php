<?php declare(strict_types=1);


namespace CoreShop\Component\Order\Model;


use CoreShop\Component\Resource\Model\ResourceInterface;

interface CartPriceRuleVoucherCodeUserInterface extends ResourceInterface
{
    public function setVoucherCode(CartPriceRuleVoucherCodeInterface $voucherCode): void;
    
    public function getVoucherCode(): CartPriceRuleVoucherCodeInterface;

    public function getUses(): int;

    public function setUses(int $uses): void;

    public function incrementUses(): void;

    public function decrementUses(): void;

    public function getCustomerId(): int;

    public function setCustomerId(int $customerId): void;
}
