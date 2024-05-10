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

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

interface WishlistItemInterface extends
    PimcoreModelInterface,
    StorageListItemInterface
{
    public function getWishlist(): ?WishlistInterface;

    public function setWishlist(WishlistInterface $wishlist);

    public function getProduct(): ?WishlistProductInterface;

    public function setProduct(?WishlistProductInterface $product);
}
