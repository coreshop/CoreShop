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

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Processor\CartItemProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Webmozart\Assert\Assert;

final class CartItemProcessor implements CartItemProcessorInterface
{
    private $taxCalculator;
    private $defaultAddressProvider;

    public function __construct(
        ProductTaxCalculatorFactoryInterface $taxCalculator,
        AddressProviderInterface $defaultAddressProvider
    ) {
        $this->taxCalculator = $taxCalculator;
        $this->defaultAddressProvider = $defaultAddressProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function processCartItem(CartItemInterface $cartItem, int $itemPrice, int $itemRetailPrice, int $itemDiscountPrice, int $itemDiscount, array $context): void
    {
        /**
         * @var \CoreShop\Component\Core\Model\CartItemInterface $cartItem
         */
        Assert::isInstanceOf($cartItem, \CoreShop\Component\Core\Model\CartItemInterface::class);

        $product = $cartItem->getProduct();
        $cart = $context['cart'];
        $store = $context['store'];

        $taxCalculator = $this->taxCalculator->getTaxCalculator($product, $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart));

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            if ($store->getUseGrossPrice()) {
                $totalTaxAmount = $taxCalculator->getTaxesAmountFromGross((int) round($itemPrice * $cartItem->getQuantity()));
                $itemPriceTax = $taxCalculator->getTaxesAmountFromGross($itemPrice);
                $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmountFromGross($itemRetailPrice);
                $itemDiscountTax = $taxCalculator->getTaxesAmountFromGross($itemDiscount);
                $itemDiscountPriceTax = $taxCalculator->getTaxesAmountFromGross($itemDiscountPrice);

                $cartItem->setTotal((int) round($itemPrice * $cartItem->getQuantity()), true);
                $cartItem->setTotal($cartItem->getTotal(true) - $totalTaxAmount, false);

                $cartItem->setItemPrice($itemPrice, true);
                $cartItem->setItemPrice($itemPrice - $itemPriceTax, false);

                $cartItem->setItemRetailPrice($itemRetailPrice, true);
                $cartItem->setItemRetailPrice($itemRetailPrice - $itemRetailPriceTaxAmount, false);

                $cartItem->setItemDiscountPrice($itemDiscountPrice, true);
                $cartItem->setItemDiscountPrice($itemDiscountPrice - $itemDiscountTax, false);

                $cartItem->setItemDiscount($itemDiscount, true);
                $cartItem->setItemDiscount($itemDiscount - $itemDiscountPriceTax, false);
            } else {
                $totalTaxAmount = $taxCalculator->getTaxesAmount((int) round($itemPrice * $cartItem->getQuantity()));
                $itemPriceTax = $taxCalculator->getTaxesAmount($itemPrice);
                $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmount($itemRetailPrice);
                $itemDiscountTax = $taxCalculator->getTaxesAmount($itemDiscount);
                $itemDiscountPriceTax = $taxCalculator->getTaxesAmount($itemDiscountPrice);

                $cartItem->setTotal((int) round($itemPrice * $cartItem->getQuantity()), false);
                $cartItem->setTotal((int) round($itemPrice * $cartItem->getQuantity()) + $totalTaxAmount, true);

                $cartItem->setItemPrice($itemPrice, false);
                $cartItem->setItemPrice($itemPrice + $itemPriceTax, true);

                $cartItem->setItemRetailPrice($itemRetailPrice, false);
                $cartItem->setItemRetailPrice($itemRetailPrice + $itemRetailPriceTaxAmount, true);

                $cartItem->setItemDiscountPrice($itemDiscountPrice, false);
                $cartItem->setItemDiscountPrice($itemDiscountPrice + $itemDiscountTax, true);

                $cartItem->setItemDiscount($itemDiscount, false);
                $cartItem->setItemDiscount($itemDiscount + $itemDiscountPriceTax, true);
            }
        } else {
            $cartItem->setTotal((int) round($itemPrice * $cartItem->getQuantity()), false);
            $cartItem->setTotal((int) round($itemPrice * $cartItem->getQuantity()), true);

            $cartItem->setItemPrice($itemPrice, true);
            $cartItem->setItemPrice($itemPrice, false);

            $cartItem->setItemRetailPrice($itemRetailPrice, false);
            $cartItem->setItemRetailPrice($itemRetailPrice, true);

            $cartItem->setItemDiscountPrice($itemDiscountPrice, false);
            $cartItem->setItemDiscountPrice($itemDiscountPrice, true);

            $cartItem->setItemDiscount($itemDiscount, false);
            $cartItem->setItemDiscount($itemDiscount, true);
        }

        if ($product instanceof ProductInterface) {
            /**
             * @var \CoreShop\Component\Core\Model\CartItemInterface $cartItem
             */
            $cartItem->setDigitalProduct($product->getDigitalProduct());
        }
    }
}
