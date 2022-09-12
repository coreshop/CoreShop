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

namespace CoreShop\Component\Wishlist\Model;

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

abstract class WishlistItem extends AbstractPimcoreModel implements WishlistItemInterface
{
    public function getStorageList(): StorageListInterface
    {
        return $this->getWishlist();
    }

    public function getWishlist(): WishlistInterface
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof WishlistInterface) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent !== null);

        throw new \Exception('Wishlist Item does not have a valid Wishlist');
    }

    public function equals(StorageListItemInterface $storageListItem): bool
    {
        return $storageListItem->getProduct() instanceof WishlistProductInterface &&
            $this->getProduct() instanceof WishlistProductInterface &&
            $storageListItem->getProduct()->getId() === $this->getProduct()->getId();
    }
}
