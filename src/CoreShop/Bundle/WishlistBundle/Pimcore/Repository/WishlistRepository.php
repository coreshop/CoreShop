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

namespace CoreShop\Bundle\WishlistBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Repository\WishlistRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class WishlistRepository extends PimcoreRepository implements WishlistRepositoryInterface
{
    public function findWishlistByCustomer(CustomerInterface $customer): array
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ?', [$customer->getId()]);
        $list->load();

        /**
         * @var WishlistInterface[] $carts
         */
        $carts = $list->getObjects();

        return $carts;
    }

    public function findByWishlistId(int $id): ?WishlistInterface
    {
        $list = $this->getList();
        $list->setCondition('o_id = ?', [$id]);
        $list->load();

        if ($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    public function findByToken(string $token): ?WishlistInterface
    {
        $list = $this->getList();
        $list->setCondition('token = ?', [$token]);
        $list->load();

        if ($list->getTotalCount() === 1) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    public function findLatestWishlistByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?WishlistInterface
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND store = ?', [$customer->getId(), $store->getId()]);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) > 0 && $objects[0] instanceof WishlistInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findByCustomer(CustomerInterface $customer): array
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ?', [$customer->getId()]);
        $list->setOrderKey('o_id');
        $list->setOrder('DESC');
        $list->load();

        return $list->getObjects();
    }

    public function hasCustomerWishlists(CustomerInterface $customer): bool
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ?', [$customer->getId()]);

        return $list->getTotalCount() > 0;
    }
}
