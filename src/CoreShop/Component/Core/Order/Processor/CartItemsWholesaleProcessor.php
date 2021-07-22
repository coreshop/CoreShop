<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Calculator\PurchasableWholesalePriceCalculatorInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartItemsWholesaleProcessor implements CartProcessorInterface
{
    private PurchasableWholesalePriceCalculatorInterface $wholesalePriceCalculator;
    private CartContextResolverInterface $cartContextResolver;

    public function __construct(
        PurchasableWholesalePriceCalculatorInterface $wholesalePriceCalculator,
        CartContextResolverInterface $cartContextResolver
    ) {
        $this->wholesalePriceCalculator = $wholesalePriceCalculator;
        $this->cartContextResolver = $cartContextResolver;
    }

    public function process(OrderInterface $cart): void
    {
        $context = $this->cartContextResolver->resolveCartContext($cart);

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            try {
                $item->setItemWholesalePrice(
                    $this->wholesalePriceCalculator->getPurchasableWholesalePrice($product, $context)
                );
            } catch (NoPurchasableWholesalePriceFoundException $ex) {
                $item->setItemWholesalePrice(0);
            }
        }
    }
}
