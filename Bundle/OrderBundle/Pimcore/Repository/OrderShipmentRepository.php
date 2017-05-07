<?php

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Repository\PimcoreRepository;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;

class OrderShipmentRepository extends PimcoreRepository implements OrderShipmentRepositoryInterface
{
    public function getDocuments(OrderInterface $order)
    {
        $list = $this->getList();
        $list->setCondition("order__id = ?", [$order->getId()]);
        $list->load();

        return $list->getObjects();
    }
}