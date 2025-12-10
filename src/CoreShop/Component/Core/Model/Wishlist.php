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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Wishlist\Model\Wishlist as BaseWishlist;

abstract class Wishlist extends BaseWishlist implements WishlistInterface
{
    public function getName(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setName(?string $name)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
