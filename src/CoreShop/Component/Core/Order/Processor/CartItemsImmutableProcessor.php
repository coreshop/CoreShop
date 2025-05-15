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

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartItemProcessorInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

class CartItemsImmutableProcessor implements CartProcessorInterface
{
    public function __construct(
        private CartItemProcessorInterface $cartItemProcessor,
        private CartContextResolverInterface $cartContextResolver,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        if (!$cart->isImmutable()) {
            return;
        }

        $context = $this->cartContextResolver->resolveCartContext($cart);

        /**
         * @var StoreInterface $store
         */
        $store = $context['store'];

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if (!$item->isImmutable()) {
                continue;
            }

            $itemPrice = $item->getItemPrice($store->getUseGrossPrice());
            $itemRetailPrice = $item->getItemRetailPrice($store->getUseGrossPrice());
            $itemDiscountPrice = $item->getItemDiscountPrice($store->getUseGrossPrice());
            $itemDiscount = $item->getItemDiscount($store->getUseGrossPrice());

            $this->cartItemProcessor->processCartItem(
                $item,
                $itemPrice,
                $itemRetailPrice,
                $itemDiscountPrice,
                $itemDiscount,
                $context,
            );

            /*
             * https://github.com/coreshop/CoreShop/issues/2572
             *
             * Since we are recalculating the items-total, we also need to respect the adjustments and
             * re-add them to the item total as well.
             */
            $item->recalculateAdjustmentsTotal();
        }
    }
}
