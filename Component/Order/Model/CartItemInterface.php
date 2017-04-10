<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CartItemInterface extends ProposalItemInterface, PimcoreModelInterface
{
    /**
     * @return bool
     */
    public function getIsGiftItem();

    /**
     * @param bool $isGiftItem
     */
    public function setIsGiftItem($isGiftItem);

    /**
     * @return CartInterface
     */
    public function getCart();
}
