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

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Factory\FactoryInterface;

final class CartContext implements CartContextInterface
{
    private FactoryInterface $cartFactory;

    public function __construct(FactoryInterface $cartFactory)
    {
        $this->cartFactory = $cartFactory;
    }

    public function getCart(): OrderInterface
    {
        /**
         * @var OrderInterface $cart
         */
        $cart = $this->cartFactory->createNew();
        $cart->setKey(uniqid());
        $cart->setPublished(true);
        $cart->setSaleState(OrderSaleStates::STATE_CART);
        $cart->setOrderState(OrderStates::STATE_INITIALIZED);
        $cart->setShippingState(OrderShipmentStates::STATE_NEW);
        $cart->setPaymentState(OrderPaymentStates::STATE_NEW);
        $cart->setInvoiceState(OrderInvoiceStates::STATE_NEW);

        return $cart;
    }
}
