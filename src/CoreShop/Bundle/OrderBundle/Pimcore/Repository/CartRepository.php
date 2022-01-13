<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
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

        /**
         * @var CartInterface[] $carts
         */
        $carts = $list->getObjects();

        return $carts;
    }

    /**
     * {@inheritdoc}
     */
    public function findNamedForCustomer(CustomerInterface $customer, $name)
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND name = ? AND order__id is null', [$customer->getId(), $name]);
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof CartInterface) {
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

        $objects = $list->getObjects();

        if (count($objects) > 0 && $objects[0] instanceof CartInterface) {
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

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof CartInterface) {
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

        $objects = $list->getObjects();

        if (count($objects) === 1 && $objects[0] instanceof CartInterface) {
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
         * @var CartInterface[] $result
         */
        $result = $list->getObjects();

        return $result;
    }
}
