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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order;

use CoreShop\Component\Order\Model\OrderInterface;

class OrderEditPossible implements OrderEditPossibleInterface
{
    public function __construct(
        private bool $orderEditEnabled,
    ) {
    }

    public function isOrderEditable(OrderInterface $order): bool
    {
        if (!$this->orderEditEnabled) {
            return false;
        }

        if ($order->getSaleState() === OrderSaleStates::STATE_ORDER) {
            /**
             * Order that has been paid, cannot be edited anymore. Changing the order also
             * means that the remaining amount has to be paid as well, that is currently not reflectable
             * Allowing that, also means that we have to find a way to do further Payments to the Order
             * Might come in the future
             */
            if ($order->getPaymentState() === OrderPaymentStates::STATE_PAID) {
                return false;
            }

            if ($order->getOrderState() === OrderStates::STATE_CANCELLED) {
                return false;
            }

            return $order->getOrderState() !== OrderStates::STATE_COMPLETE;
        }

        return true;
    }
}
