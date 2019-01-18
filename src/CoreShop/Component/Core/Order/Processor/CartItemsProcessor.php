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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartItemProcessorInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use Webmozart\Assert\Assert;

final class CartItemsProcessor implements CartProcessorInterface
{
    /**
     * @var PurchasableCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var CartItemProcessorInterface
     */
    private $cartItemProcessor;

    /**
     * @param PurchasableCalculatorInterface $productPriceCalculator
     * @param CartItemProcessorInterface     $cartItemProcessor
     */
    public function __construct(
        PurchasableCalculatorInterface $productPriceCalculator,
        CartItemProcessorInterface $cartItemProcessor
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
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
            'store' => $store,
            'customer' => $cart->getCustomer() ?: null,
            'currency' => $cart->getCurrency(),
            'country' => $store->getBaseCountry(),
            'cart' => $cart,
        ];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $itemPrice = $this->productPriceCalculator->getPrice($item->getProduct(), $context, true);

            $this->cartItemProcessor->processCartItem($item, $itemPrice, $context);
        }
    }
}
