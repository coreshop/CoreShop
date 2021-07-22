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

namespace CoreShop\Component\Order\Processor;

use CoreShop\Component\Order\Model\CartItemInterface;

interface CartItemProcessorInterface
{
    /**
     * @param CartItemInterface $cartItem
     * @param int               $itemPrice
     * @param int               $itemRetailPrice
     * @param int               $itemDiscountPrice
     * @param int               $itemDiscount
     * @param array             $context
     */
    public function processCartItem(
        CartItemInterface $cartItem,
        int $itemPrice,
        int $itemRetailPrice,
        int $itemDiscountPrice,
        int $itemDiscount,
        array $context
    );
}
