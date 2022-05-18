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

namespace CoreShop\Bundle\WishlistBundle\DTO;

use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;

class AddToWishlist implements AddToWishlistInterface
{
    public function __construct(private WishlistInterface $wishlist, private WishlistItemInterface $wishlistItem)
    {
    }

    public function getWishlist(): WishlistInterface
    {
        return $this->wishlist;
    }

    public function setWishlist(WishlistInterface $wishlist): void
    {
        $this->wishlist = $wishlist;
    }

    public function getWishlistItem(): WishlistItemInterface
    {
        return $this->wishlistItem;
    }

    public function setWishlistItem(WishlistItemInterface $wishlistItem): void
    {
        $this->wishlistItem = $wishlistItem;
    }
}
