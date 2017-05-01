<?php

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;

interface OrderDocumentItemTransformerInterface
{
    /**
     * @param OrderDocumentInterface $orderDocument
     * @param OrderItemInterface $orderItem
     * @param OrderDocumentItemInterface $documentItem
     * @param $quantity
     * @return mixed
     */
    public function transform(OrderDocumentInterface $orderDocument, OrderItemInterface $orderItem, OrderDocumentItemInterface $documentItem, $quantity);

}