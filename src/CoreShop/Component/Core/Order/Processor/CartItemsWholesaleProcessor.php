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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Calculator\PurchasableWholesalePriceCalculatorInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartItemsWholesaleProcessor implements CartProcessorInterface
{
    public function __construct(
        private PurchasableWholesalePriceCalculatorInterface $wholesalePriceCalculator,
        private CartContextResolverInterface $cartContextResolver,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        if ($cart->isImmutable()) {
            return;
        }

        $context = $this->cartContextResolver->resolveCartContext($cart);

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if ($item->isImmutable()) {
                continue;
            }

            $product = $item->getProduct();

            try {
                $item->setItemWholesalePrice(
                    $this->wholesalePriceCalculator->getPurchasableWholesalePrice($product, $context),
                );
            } catch (NoPurchasableWholesalePriceFoundException) {
                $item->setItemWholesalePrice(0);
            }
        }
    }
}
