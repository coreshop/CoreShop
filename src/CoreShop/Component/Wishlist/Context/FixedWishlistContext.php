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

namespace CoreShop\Component\Wishlist\Context;

use CoreShop\Component\Wishlist\Model\WishlistInterface;

final class FixedWishlistContext implements WishlistContextInterface
{
    private ?WishlistInterface $wishlist = null;

    public function getWishlist(): WishlistInterface
    {
        if ($this->wishlist instanceof WishlistInterface) {
            return $this->wishlist;
        }

        throw new WishlistNotFoundException();
    }

    public function setWishlist(WishlistInterface $wishlist): void
    {
        $this->wishlist = $wishlist;
    }
}
