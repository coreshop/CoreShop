<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\StorageList\Core\Repository\CustomerAndStoreAwareRepositoryInterface;
use CoreShop\Component\StorageList\Repository\PimcoreStorageListRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface OrderRepositoryInterface extends PimcoreRepositoryInterface, CustomerAndStoreAwareRepositoryInterface, PimcoreStorageListRepositoryInterface
{
    public function findCartByCustomer(CustomerInterface $customer): array;

    public function findByCartId(int $id): ?OrderInterface;

    public function findByToken(string $token): ?OrderInterface;

    public function findLatestCartByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?OrderInterface;

    public function findExpiredCarts(int $days, bool $anonymous, bool $customer): array;

    public function findByCustomer(CustomerInterface $customer): array;

    public function findOrdersByCustomer(CustomerInterface $customer): array;

    public function hasCustomerOrders(CustomerInterface $customer): bool;

    public function findExpiredOrders(int $days): array;
}
