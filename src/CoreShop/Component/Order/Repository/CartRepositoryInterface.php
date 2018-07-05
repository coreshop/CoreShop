<?php

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface CartRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * @param CustomerInterface $customer
     *
     * @return CartInterface[]
     */
    public function findForCustomer(CustomerInterface $customer);

    /**
     * @param CustomerInterface $customer
     * @param string            $name
     *
     * @return CartInterface[]
     */
    public function findNamedForCustomer(CustomerInterface $customer, $name);

    /**
     * @param StoreInterface    $store
     * @param CustomerInterface $customer
     *
     * @return CartInterface
     */
    public function findLatestByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer);

    /**
     * @param $id
     *
     * @return CartInterface|null
     */
    public function findCartById($id);

    /**
     * @param OrderInterface $order
     *
     * @return CartInterface|null
     */
    public function findCartByOrder(OrderInterface $order);

    /**
     * @param int  $days
     * @param bool $anonymous
     * @param bool $customer
     *
     * @return CartInterface[]
     */
    public function findExpiredCarts($days, $anonymous, $customer);
}
