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

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface OrderRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * @param CustomerInterface $customer
     *
     * @return OrderInterface[]
     */
    public function findCartByCustomer(CustomerInterface $customer): array;

    /**
     * @param int $id
     *
     * @return OrderInterface|null
     */
    public function findByCartId(int $id): ?OrderInterface;

     /**
     * @param StoreInterface    $store
     * @param CustomerInterface $customer
     *
     * @return OrderInterface|null
     */
    public function findLatestCartByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?OrderInterface;

    /**
     * @param int  $days
     * @param bool $anonymous
     * @param bool $customer
     *
     * @return OrderInterface[]
     */
    public function findExpiredCarts(int $days, bool $anonymous, bool $customer);

    /**
     * @param CustomerInterface $customer
     *
     * @return OrderInterface[]
     */
    public function findByCustomer(CustomerInterface $customer);

    /**
     * @param CustomerInterface $customer
     *
     * @return bool
     */
    public function hasCustomerOrders(CustomerInterface $customer);

    /**
     * @param int $days
     *
     * @return OrderInterface[]
     */
    public function findExpiredOrders($days);
}
