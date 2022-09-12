<?php
declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface PriceRuleItemInterface extends ResourceInterface
{
    public function getCartPriceRule(): ?CartPriceRuleInterface;

    public function setCartPriceRule(?CartPriceRuleInterface $cartPriceRule);

    public function getVoucherCode(): ?string;

    public function setVoucherCode(?string $voucherCode);

    public function getDiscount(bool $withTax = true): int;

    public function setDiscount(int $discount, bool $withTax = true);
}
