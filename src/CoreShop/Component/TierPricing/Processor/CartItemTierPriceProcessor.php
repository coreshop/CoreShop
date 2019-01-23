<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\TierPricing\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Order\Processor\CartItemsProcessor;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Processor\CartItemProcessorInterface;
use CoreShop\Component\TierPricing\Rule\Calculator\ProductTierPriceCalculatorInterface;
use Webmozart\Assert\Assert;

class CartItemTierPriceProcessor implements CartProcessorInterface
{
    /**
     * @var CartItemsProcessor
     */
    protected $innerCartProcessor;

    /**
     * @var PurchasableCalculatorInterface
     */
    protected $productPriceCalculator;

    /**
     * @var ProductTierPriceCalculatorInterface
     */
    protected $productTierPriceCalculator;

    /**
     * @var CartItemProcessorInterface
     */
    protected $cartItemProcessor;

    /**
     * @param CartProcessorInterface              $innerCartProcessor
     * @param PurchasableCalculatorInterface      $productPriceCalculator
     * @param CartItemProcessorInterface          $cartItemProcessor
     * @param ProductTierPriceCalculatorInterface $productTierPriceCalculator
     */
    public function __construct(
        CartProcessorInterface $innerCartProcessor,
        PurchasableCalculatorInterface $productPriceCalculator,
        ProductTierPriceCalculatorInterface $productTierPriceCalculator,
        CartItemProcessorInterface $cartItemProcessor
    ) {
        $this->innerCartProcessor = $innerCartProcessor;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->productTierPriceCalculator = $productTierPriceCalculator;
        $this->cartItemProcessor = $cartItemProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $store = $cart->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        $context = [
            'store'    => $store,
            'customer' => $cart->getCustomer() ?: null,
            'currency' => $cart->getCurrency(),
            'country'  => $store->getBaseCountry(),
            'cart'     => $cart,
        ];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {

            $tierItemPrice = $this->productTierPriceCalculator->getTierPriceForCartItem($item->getProduct(), $item, $context);

            if ($tierItemPrice !== false) {
                $itemPrice = $tierItemPrice;
            } else {
                $itemPrice = $this->productPriceCalculator->getPrice($item->getProduct(), $context, true);
            }

            $itemPriceWithoutDiscount = $this->productPriceCalculator->getPrice($item->getProduct(), $context);
            $itemRetailPrice = $this->productPriceCalculator->getRetailPrice($item->getProduct(), $context);
            $itemDiscountPrice = $this->productPriceCalculator->getDiscountPrice($item->getProduct(), $context);
            $itemDiscount = $this->productPriceCalculator->getDiscount($item->getProduct(), $context, $itemPriceWithoutDiscount);

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