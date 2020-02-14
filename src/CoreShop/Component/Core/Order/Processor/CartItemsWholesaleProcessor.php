<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Order\Calculator\PurchasableWholesalePriceCalculatorInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartItemsWholesaleProcessor implements CartProcessorInterface
{
    private $cartContextResolver;
    private $wholesalePriceCalculator;

    public function __construct(
        PurchasableWholesalePriceCalculatorInterface $wholesalePriceCalculator,
        CartContextResolverInterface $cartContextResolver
    ) {
        $this->wholesalePriceCalculator = $wholesalePriceCalculator;
        $this->cartContextResolver = $cartContextResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $cart): void
    {
        $context = $this->cartContextResolver->resolveCartContext($cart);

        /**
         * @var CartItemInterface $item
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
