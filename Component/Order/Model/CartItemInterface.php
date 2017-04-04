<?php

namespace CoreShop\Component\Order\Model;

interface CartItemInterface extends ProposalItemInterface
{
    /**
     * @return boolean
     */
    public function getIsGiftItem();

    /**
     * @param boolean $isGiftItem
     */
    public function setIsGiftItem($isGiftItem);
}