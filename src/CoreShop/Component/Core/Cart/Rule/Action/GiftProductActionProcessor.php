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

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepositoryInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Factory\OrderItemFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Rule\Model\ActionInterface;

final class GiftProductActionProcessor implements CartPriceRuleActionProcessorInterface
{
    public function __construct(
        private StackRepositoryInterface $productRepository,
        private OrderItemFactoryInterface $cartItemFactory,
        private AdjustmentFactoryInterface $adjustmentFactory,
    ) {
    }

    public function applyRule(OrderInterface $cart, array $configuration, PriceRuleItemInterface $cartPriceRuleItem): bool
    {
        $product = $this->productRepository->find($configuration['product']);

        if (!$product instanceof PurchasableInterface) {
            return false;
        }

        $action = $configuration['action'];
        $key = $this->getKey($action);
        $cartItems = [];

        foreach ($cart->getItems() as $item) {
            if (!$item->getIsGiftItem()) {
                continue;
            }

            foreach ($item->getAdjustments() as $adjustment) {
                if ($adjustment->getTypeIdentifier() === $key) {
                    $cartItems[] = $item;
                }
            }
        }

        foreach ($cartItems as $cartItem) {
            $this->removeCartItem($cart, $cartItem);
        }

        $item = $this->cartItemFactory->createWithCart($cart, $product);
        $item->setQuantity(1);
        $item->setIsGiftItem(true);

        $adjustment = $this->adjustmentFactory->createWithData(
            $key,
            $cartPriceRuleItem->getCartPriceRule()->getName(),
            0,
            0,
            true,
        );

        $item->addAdjustment($adjustment);

        return true;
    }

    public function unApplyRule(OrderInterface $cart, array $configuration, PriceRuleItemInterface $cartPriceRuleItem): bool
    {
        $product = $this->productRepository->find($configuration['product']);

        if (!$product instanceof PurchasableInterface) {
            return false;
        }

        $action = $configuration['action'];
        $key = $this->getKey($action);
        $cartItems = [];

        foreach ($cart->getItems() as $item) {
            if (!$item->getIsGiftItem()) {
                continue;
            }

            foreach ($item->getAdjustments() as $adjustment) {
                if ($adjustment->getTypeIdentifier() === $key) {
                    $cartItems[] = $item;
                }
            }
        }

        foreach ($cartItems as $cartItem) {
            $this->removeCartItem($cart, $cartItem);
        }

        return true;
    }

    private function removeCartItem(OrderInterface $cart, OrderItemInterface $cartItem): void
    {
        $cart->removeItem($cartItem);

        if ($cartItem->getId() === null) {
            return;
        }

        if ($cartItem->getId() === 0) {
            return;
        }

        $cartItem->delete();
    }

    private function getKey(ActionInterface $action): string
    {
        return sprintf('%s_%s', AdjustmentInterface::CART_PRICE_RULE, $action->getId());
    }
}
