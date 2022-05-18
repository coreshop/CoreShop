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

declare(strict_types=1);

namespace CoreShop\Component\Wishlist\Repository;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface WishlistRepositoryInterface extends PimcoreRepositoryInterface
{
    public function findWishlistByCustomer(CustomerInterface $customer): array;

    public function findByWishlistId(int $id): ?WishlistInterface;

    public function findByToken(string $token): ?WishlistInterface;

    public function findLatestWishlistByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?WishlistInterface;

    public function findByCustomer(CustomerInterface $customer): array;

    public function hasCustomerWishlists(CustomerInterface $customer): bool;
}
