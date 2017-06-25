<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\CartModifierInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

use CoreShop\Component\Product\Repository\ProductRepositoryInterface;

final class GiftProductActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CartModifierInterface
     */
    private $cartModifier;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CartModifierInterface      $cartModifier
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

        if ($product instanceof PurchasableInterface) {
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

        if ($product instanceof PurchasableInterface) {
            $this->cartModifier->updateCartItemQuantity($cart, $product, 0, false);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(CartInterface $cart, $withTax, array $configuration)
    {
        return 0;
    }
}
