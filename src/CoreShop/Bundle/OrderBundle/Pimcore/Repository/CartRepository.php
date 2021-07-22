<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class CartRepository extends PimcoreRepository implements CartRepositoryInterface
{
    public function findForCustomer(CustomerInterface $customer): array
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND order__id is null', [$customer->getId()]);
        $list->load();

        /**
         * @var OrderInterface[] $carts
         */
        $carts = $list->getObjects();

        return $carts;
    }

    public function findNamedForCustomer(CustomerInterface $customer, $name): ?OrderInterface
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND name = ? AND order__id is null', [$customer->getId(), $name]);
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof OrderInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findLatestByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?OrderInterface
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND store = ? AND order__id is null ', [$customer->getId(), $store->getId()]);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof OrderInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findCartById($id): ?OrderInterface
    {
        $list = $this->getList();
        $list->setCondition('o_id = ? AND order__id is null ', [$id]);
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof OrderInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findCartByOrder(OrderInterface $order): ?OrderInterface
    {
        $list = $this->getList();
        $list->setCondition('order__id = ? ', [$order->getId()]);
        $list->setLimit(1);
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof OrderInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findExpiredCarts($days, $anonymous, $customer): array
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

        /**
         * @var OrderInterface[] $result
         */
        $result = $list->getObjects();

        return $result;
    }
}
