<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
