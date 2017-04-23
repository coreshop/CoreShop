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
     * @return float
     */
    public function getDiscount();

    /**
     * @param float $discount
     */
    public function setDiscount($discount);
}