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
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

class WishlistItemFactory implements WishlistItemFactoryInterface
{
    public function __construct(private FactoryInterface $wishlistItemFactory)
    {
    }

    public function createNew()
    {
        return $this->wishlistItemFactory->createNew();
    }

    public function createWithWishlist(WishlistInterface $wishlist, PurchasableInterface $purchasable): WishlistItemInterface
    {
        $item = $this->wishlistItemFactory->createNew();
        $item->setKey(uniqid());
        $item->setParent($wishlist);
        $item->setProduct($purchasable);
        $item->setPublished(true);

        $wishlist->addItem($item);

        return $item;
    }

    public function createWithPurchasable(PurchasableInterface $purchasable): WishlistItemInterface
    {
        $item = $this->wishlistItemFactory->createNew();
        $item->setKey(uniqid());
        $item->setProduct($purchasable);
        $item->setPublished(true);

        return $item;
    }
}
