<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
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
    public function hasCustomerOrders(CustomerInterface $customer)
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ?', [$customer->getId()]);

        return $list->getTotalCount() > 0;
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

        /**
         * @var OrderInterface[] $result
         */
        $result = $list->getObjects();

        return $result;
    }
}
