<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
