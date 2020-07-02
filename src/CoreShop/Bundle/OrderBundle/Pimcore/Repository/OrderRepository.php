<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class OrderRepository extends PimcoreRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCartByCustomer(CustomerInterface $customer): array
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND saleState = ?', [$customer->getId(), OrderSaleStates::STATE_CART]);
        $list->load();

        /**
         * @var OrderInterface[] $carts
         */
        $carts = $list->getObjects();

        return $carts;
    }

    public function findByCartId(int $id): ?OrderInterface
    {
        $list = $this->getList();
        $list->setCondition('o_id = ? AND saleState = ? ', [$id, OrderSaleStates::STATE_CART]);
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
    public function findLatestCartByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?OrderInterface
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND store = ? AND saleState = ? ', [$customer->getId(), $store->getId(), OrderSaleStates::STATE_CART]);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof OrderInterface) {
            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findExpiredCarts(int $days, bool $anonymous, bool $customer): array
    {
        $list = $this->getList();

        $conditions = [];
        $groupCondition = [];
        $params = [];

        $daysTimestamp = Carbon::now();
        $daysTimestamp->subDays($days);

        $conditions[] = 'o_creationDate < ?';
        $params[] = $daysTimestamp->getTimestamp();

        //Never delete carts with a order
        $conditions[] = 'saleState = ?';
        $params[] = OrderSaleStates::STATE_CART;

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

        /**
         * @var OrderInterface[] $result
         */
        $result = $list->getObjects();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findByCustomer(CustomerInterface $customer): array
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
    public function hasCustomerOrders(CustomerInterface $customer): bool
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ?', [$customer->getId()]);

        return $list->getTotalCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function findExpiredOrders(int $days): array
    {
        $daysTimestamp = Carbon::now();
        $daysTimestamp->subDays($days);

        $conditions[] = 'o_creationDate < ? AND saleState = ? AND orderState IN (?, ?, ?) AND paymentState <> ?';
        $params[] = $daysTimestamp->getTimestamp();
        $params[] = OrderSaleStates::STATE_ORDER;
        $params[] = OrderStates::STATE_NEW;
        $params[] = OrderStates::STATE_CONFIRMED;
        $params[] = OrderStates::STATE_INITIALIZED;
        $params[] = OrderPaymentStates::STATE_PAID;

        $bind = ' AND ';

        $sql = implode($bind, $conditions);

        $list = $this->getList();
        $list->setCondition($sql, $params);

        /**
         * @var OrderInterface[] $result
         */
        $result = $list->getObjects();

        return $result;
    }
}
