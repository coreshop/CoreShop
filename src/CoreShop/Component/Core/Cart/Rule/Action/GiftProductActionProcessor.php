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

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Factory\CartItemFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;

final class GiftProductActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CartItemFactoryInterface
     */
    private $cartItemFactory;

    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CartItemFactoryInterface   $cartItemFactory
     * @param AdjustmentFactoryInterface $adjustmentFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CartItemFactoryInterface $cartItemFactory,
        AdjustmentFactoryInterface $adjustmentFactory
    ) {
        $this->productRepository = $productRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function applyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem)
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
    public function unApplyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem)
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

    /**
     * @param CartInterface     $cart
     * @param CartItemInterface $cartItem
     */
    private function removeCartItem(CartInterface $cart, CartItemInterface $cartItem)
    {
        $cart->removeItem($cartItem);
        $cartItem->delete();
    }

    /**
     * @param ActionInterface $action
     *
     * @return string
     */
    private function getKey(ActionInterface $action)
    {
        return sprintf('%s_%s', AdjustmentInterface::CART_PRICE_RULE, $action->getId());
    }
}
