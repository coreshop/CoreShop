<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProposalCartPriceRuleItemInterface extends ResourceInterface
{
    /**
     * @return CartPriceRuleInterface
     */
    public function getCartPriceRule();

    /**
     * @param CartPriceRuleInterface $cartPriceRule
     */
    public function setCartPriceRule($cartPriceRule);

    /**
     * @return string
     */
    public function getVoucherCode();

    /**
     * @param string $voucherCode
     */
    public function setVoucherCode($voucherCode);

    /**
     * @param bool $withTax
     * @return float
     */
    public function getDiscount($withTax = true);

    /**
     * @param float $discount
     * @param boolean $withTax
     */
    public function setDiscount($discount, $withTax = true);
}