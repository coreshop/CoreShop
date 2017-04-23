<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface CartPriceRuleVoucherCodeInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate($creationDate);

    /**
     * @return boolean
     */
    public function getUsed();

    /**
     * @param boolean $used
     */
    public function setUsed($used);

    /**
     * @return int
     */
    public function getUses();

    /**
     * @param int $uses
     */
    public function setUses($uses);

    /**
     * @return CartPriceRuleInterface
     */
    public function getCartPriceRule();

    /**
     * @param CartPriceRuleInterface $cartPriceRule
     */
    public function setCartPriceRule($cartPriceRule);
}
