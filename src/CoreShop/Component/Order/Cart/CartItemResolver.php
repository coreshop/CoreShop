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

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;

class CartItemResolver implements StorageListItemResolverInterface
{
    public function equals(StorageListItemInterface $itemA, StorageListItemInterface $itemB): bool
    {
        return $itemA->equals($itemB);
    }
}
