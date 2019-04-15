<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Modifier;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use Webmozart\Assert\Assert;

class CartItemQuantityModifier implements StorageListItemQuantityModifierInterface
{
    public function modify(StorageListItemInterface $item, int $targetQuantity)
    {
        /**
         * @var CartItemInterface $item
         */
        Assert::isInstanceOf($item, CartItemInterface::class);

        $item->setQuantity($targetQuantity);

        if ($item->hasUnitDefinition()) {
            $item->setDefaultUnitQuantity($item->getUnitDefinition()->getConversionRate() * $item->getQuantity());
        } else {
            $item->setDefaultUnitQuantity($item->getQuantity());
        }
    }
}
