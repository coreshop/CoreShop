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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartSubtotalProcessor implements CartProcessorInterface
{
    public function process(OrderInterface $cart): void
    {
        $subtotalGross = 0;
        $subtotalNet = 0;

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            /*
             * https://github.com/coreshop/CoreShop/issues/2572
             *
             * We have to use the subtotal here.
             *
             * The difference between $item->getTotal and $item->getSubtotal is,
             * that the total includes all applied adjustments from cart rules.
             *
             * We do that since we need to calculate tax for cart rules and split
             * the rule amount onto the items.
             *
             * We also need to handle cart rule values for returns and credit-memos.
             */
            $subtotalGross += $item->getSubtotal(true);
            $subtotalNet += $item->getSubtotal(false);
        }

        $cart->setSubtotal($subtotalGross, true);
        $cart->setSubtotal($subtotalNet, false);

        $cart->recalculateAdjustmentsTotal();
    }
}
