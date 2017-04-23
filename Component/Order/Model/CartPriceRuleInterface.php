<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\Common\Collections\Collection;

interface CartPriceRuleInterface extends RuleInterface
{
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     *
     * @return static
     */
    public function setDescription($description);

    /**
     * @return bool
     */
    public function getActive();

    /**
     * @param bool $active
     *
     * @return static
     */
    public function setActive($active);

    /**
     * @return boolean
     */
    public function getHighlight();

    /**
     * @param boolean $highlight
     */
    public function setHighlight($highlight);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return boolean
     */
    public function getUseMultipleVoucherCodes();

    /**
     * @param boolean $useMultipleVoucherCodes
     */
    public function setUseMultipleVoucherCodes($useMultipleVoucherCodes);

    /**
     * @return Collection|CartPriceRuleVoucherCodeInterface[]
     */
    public function getVoucherCodes();

    /**
     * @return bool
     */
    public function hasVoucherCodes();

    /**
     * @param CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode
     */
    public function addVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode);

    /**
     * @param CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode
     */
    public function removeVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode);

    /**
     * @param CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode
     *
     * @return bool
     */
    public function hasVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode);
}
