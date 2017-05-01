<?php

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;

interface OrderDocumentTransformerInterface {

    /**
     * Transforms an order to an invoice
     *
     * @param OrderInterface $order
     * @param OrderDocumentInterface $document
     * @param $items
     * @return mixed
     */
    public function transform(OrderInterface $order, OrderDocumentInterface $document, $items);

}