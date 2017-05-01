<?php

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

interface OrderDocumentRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * @param OrderInterface $order
     * @return OrderDocumentInterface[]
     */
    public function getDocuments(OrderInterface $order);
}