<?php

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\CartModifierInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface;

final class GiftProductActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CartModifierInterface
     */
    protected $cartModifier;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CartModifierInterface $cartModifier
     */
    public function __construct(ProductRepositoryInterface $productRepository, CartModifierInterface $cartModifier)
    {
        $this->productRepository = $productRepository;
        $this->cartModifier = $cartModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function applyRule(CartInterface $cart, array $configuration)
    {
        $product = $this->productRepository->find($configuration['product']);

        if ($product instanceof ProductInterface) {
            $this->cartModifier->updateCartItemQuantity($cart, $product, 1, false);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(CartInterface $cart, array $configuration)
    {
        $product = $this->productRepository->find($configuration['product']);

        if ($product instanceof ProductInterface) {
            $this->cartModifier->updateCartItemQuantity($cart, $product, 0, false);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(CartInterface $cart, $withTax = true, array $configuration)
    {
        return 0;
    }
}
