<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface OrderDocumentItemInterface extends ResourceInterface, PimcoreModelInterface
{
    /**
     * @return OrderInterface
     */
    public function getDocument();

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem();

    /**
     * @param OrderItemInterface $orderItem
     */
    public function setOrderItem($orderItem);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $amount
     */
    public function setQuantity($amount);
}