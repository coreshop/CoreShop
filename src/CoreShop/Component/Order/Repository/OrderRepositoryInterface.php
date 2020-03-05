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
    public function findCartByCustomer(CustomerInterface $customer): array;

    public function findByCartId(int $id): ?OrderInterface;

    public function findLatestCartByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?OrderInterface;

    public function findExpiredCarts(int $days, bool $anonymous, bool $customer): array;

    public function findByCustomer(CustomerInterface $customer): array;

    public function hasCustomerOrders(CustomerInterface $customer): bool;

    public function findExpiredOrders(int $days): array;
}
