<?php

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;

class OrderRepository extends PimcoreRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByCustomer(CustomerInterface $customer)
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ?', [$customer->getId()]);
        $list->setOrderKey('o_id');
        $list->setOrder('DESC');
        $list->load();

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findExpiredOrders($days)
    {
        $daysTimestamp = Carbon::now();
        $daysTimestamp->subDay($days);

        $conditions[] = 'o_creationDate < ? AND orderState IN (?, ?, ?) AND paymentState <> ?';
        $params[] = $daysTimestamp->getTimestamp();
        $params[] = OrderStates::STATE_NEW;
        $params[] = OrderStates::STATE_CONFIRMED;
        $params[] = OrderStates::STATE_INITIALIZED;
        $params[] = OrderPaymentStates::STATE_PAID;

        $bind = ' AND ';

        $sql = implode($bind, $conditions);

        $list = $this->getList();
        $list->setCondition($sql, $params);

        return $list->load();
    }
}
