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

namespace CoreShop\Component\Wishlist\Factory;

use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItem;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Wishlist\Model\WishlistProductInterface;

class WishlistItemFactory implements WishlistItemFactoryInterface
{
    public function __construct(private FactoryInterface $wishlistItemFactory)
    {
    }

    public function createNew()
    {
        return $this->wishlistItemFactory->createNew();
    }

    public function createWithWishlist(WishlistProductInterface $wishlistProduct): WishlistItemInterface
    {
        $item = $this->wishlistItemFactory->createNew();
        $item->setKey(uniqid());
        $item->setProduct($wishlistProduct);
        $item->setPublished(true);

        return $item;
    }
}
