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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class StorageListItemQuantityModifier implements StorageListItemQuantityModifierInterface
{
    public function modify(StorageListItemInterface $item, float $targetQuantity): void
    {
        $currentQuantity = $item->getQuantity();
        if (0 >= $targetQuantity || $currentQuantity === $targetQuantity) {
            return;
        }

        $item->setQuantity($targetQuantity);
    }
}
