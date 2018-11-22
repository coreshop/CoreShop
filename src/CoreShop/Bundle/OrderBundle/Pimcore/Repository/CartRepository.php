<?php

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class CartRepository extends PimcoreRepository implements CartRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForCustomer(CustomerInterface $customer)
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND order__id is null', [$customer->getId()]);
        $list->load();

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findNamedForCustomer(CustomerInterface $customer, $name)
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND name = ? AND order__id is null', [$customer->getId(), $name]);
        $list->load();

        if ($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer)
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND store = ? AND order__id is null ', [$customer->getId(), $store->getId()]);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');
        $list->load();

        if ($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findCartById($id)
    {
        $list = $this->getList();
        $list->setCondition('o_id = ? AND order__id is null ', [$id]);
        $list->load();

        if ($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findCartByOrder(OrderInterface $order)
    {
        $list = $this->getList();
        $list->setCondition('order__id = ? ', [$order->getId()]);
        $list->setLimit(1);
        $list->load();

        if ($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findExpiredCarts($days, $anonymous, $customer)
    {
        $list = $this->getList();

        $conditions = [];
        $groupCondition = [];
        $params = [];

        $daysTimestamp = Carbon::now();
        $daysTimestamp->subDay($days);

        $conditions[] = 'o_creationDate < ?';
        $params[] = $daysTimestamp->getTimestamp();

        //Never delete carts with a order
        $conditions[] = 'order__id IS NULL';

        if (true === $anonymous) {
            $groupCondition[] = 'customer__id IS NULL';
        }

        if (true === $customer) {
            $groupCondition[] = 'customer__id IS NOT NULL';
        }

        $bind = ' AND ';
        $groupBind = ' OR ';

        $sql = implode($bind, $conditions);

        if (count($groupCondition) > 1) {
            $groupBind = ' OR ';
        }

        $sql .= ' AND (' . implode($groupBind, $groupCondition) . ') ';

        $list->setCondition($sql, $params);

        return $list->load();
    }
}
