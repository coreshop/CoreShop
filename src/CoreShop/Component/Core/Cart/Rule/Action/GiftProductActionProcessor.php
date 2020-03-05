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

declare(strict_types=1);

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Factory\OrderItemFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;

final class GiftProductActionProcessor implements CartPriceRuleActionProcessorInterface
{
    private $productRepository;
    private $cartItemFactory;
    private $adjustmentFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        OrderItemFactoryInterface $cartItemFactory,
        AdjustmentFactoryInterface $adjustmentFactory
    ) {
        $this->productRepository = $productRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function applyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool
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

        $item = $this->cartItemFactory->createWithCart($cart, $product, 1);
        $item->setIsGiftItem(true);

        $adjustment = $this->adjustmentFactory->createWithData(
            $key,
            $cartPriceRuleItem->getCartPriceRule()->getName(),
            0,
            0,
            true
        );

        $item->addAdjustment($adjustment);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool
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
        $cartItem->delete();
    }

    private function getKey(ActionInterface $action): string
    {
        return sprintf('%s_%s', AdjustmentInterface::CART_PRICE_RULE, $action->getId());
    }
}
