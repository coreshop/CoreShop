<?php

namespace CoreShop\Component\Order\Model;

interface CartItemInterface extends ProposalItemInterface
{
    /**
     * @return bool
     */
    public function getIsGiftItem();

    /**
     * @param bool $isGiftItem
     */
    public function setIsGiftItem($isGiftItem);
}
