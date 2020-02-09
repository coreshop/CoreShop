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

use CoreShop\Component\Cart\Cart\CartContextResolverInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartItemProcessorInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\ProductQuantityPriceRules\Detector\QuantityReferenceDetectorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use Webmozart\Assert\Assert;

final class CartItemsProcessor implements CartProcessorInterface
{
    /**
     * @var CartContextResolverInterface
     */
    private $cartContextResolver;

    /**
     * @var PurchasableCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var QuantityReferenceDetectorInterface
     */
    private $quantityReferenceDetector;

    /**
     * @var CartItemProcessorInterface
     */
    private $cartItemProcessor;

    /**
     * @param PurchasableCalculatorInterface     $productPriceCalculator
     * @param QuantityReferenceDetectorInterface $quantityReferenceDetector
     * @param CartItemProcessorInterface         $cartItemProcessor
     * @param CartContextResolverInterface       $cartContextResolver
     */
    public function __construct(
        PurchasableCalculatorInterface $productPriceCalculator,
        QuantityReferenceDetectorInterface $quantityReferenceDetector,
        CartItemProcessorInterface $cartItemProcessor,
        CartContextResolverInterface $cartContextResolver = null
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->quantityReferenceDetector = $quantityReferenceDetector;
        $this->cartItemProcessor = $cartItemProcessor;
        $this->cartContextResolver = $cartContextResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        if (null === $this->cartContextResolver) {
            @trigger_error(
                'Using CartItemsProcessor without a CartContextResolverInterface is deprecated since 2.1.2 and will be removed with 3.0.0',
                E_USER_DEPRECATED
            );

            $store = $cart->getStore();

            /**
             * @var StoreInterface $store
             */
            Assert::isInstanceOf($store, StoreInterface::class);

            $context = [
                'store' => $store,
                'customer' => $cart->getCustomer() ?: null,
                'currency' => $cart->getCurrency(),
                'country' => $store->getBaseCountry(),
                'cart' => $cart,
            ];
        } else {
            $context = $this->cartContextResolver->resolveCartContext($cart);
        }

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if ($item->getIsGiftItem()) {
                $this->cartItemProcessor->processCartItem(
                    $item,
                    0,
                    0,
                    0,
                    0,
                    $context
                );

                continue;
            }

            $product = $item->getProduct();

            if ($item->hasUnitDefinition()) {
                $context['unitDefinition'] = $item->getUnitDefinition();
            } else {
                unset($context['unitDefinition']);
            }

            $itemPrice = $this->productPriceCalculator->getPrice($product, $context, true);

            // respect item quantity factor
            if ($product instanceof ProductInterface && is_numeric($product->getItemQuantityFactor()) && $product->getItemQuantityFactor() > 1) {
                $itemPrice = (int)round($itemPrice / (int) $product->getItemQuantityFactor());
            }

            if ($product instanceof QuantityRangePriceAwareInterface) {
                try {
                    $itemPrice = $this->quantityReferenceDetector->detectQuantityPrice($product, $item->getQuantity(),
                        $itemPrice, $context);
                } catch (NoRuleFoundException $exception) {
                } catch (NoPriceFoundException $exception) {
                }
            }

            $itemPriceWithoutDiscount = $this->productPriceCalculator->getPrice($product, $context);
            $itemRetailPrice = $this->productPriceCalculator->getRetailPrice($product, $context);
            $itemDiscountPrice = $this->productPriceCalculator->getDiscountPrice($product, $context);
            $itemDiscount = $this->productPriceCalculator->getDiscount($product, $context, $itemPriceWithoutDiscount);

            $this->cartItemProcessor->processCartItem(
                $item,
                $itemPrice,
                $itemRetailPrice,
                $itemDiscountPrice,
                $itemDiscount,
                $context
            );
        }
    }
}
