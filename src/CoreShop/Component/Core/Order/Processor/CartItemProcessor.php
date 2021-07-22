<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Distributor\IntegerDistributor;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartItemProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Webmozart\Assert\Assert;

final class CartItemProcessor implements CartItemProcessorInterface
{
    private ProductTaxCalculatorFactoryInterface $taxCalculator;
    private AddressProviderInterface $defaultAddressProvider;
    private IntegerDistributor $integerDistributor;

    public function __construct(
        ProductTaxCalculatorFactoryInterface $taxCalculator,
        AddressProviderInterface $defaultAddressProvider,
        IntegerDistributor $integerDistributor
    ) {
        $this->taxCalculator = $taxCalculator;
        $this->defaultAddressProvider = $defaultAddressProvider;
        $this->integerDistributor = $integerDistributor;
    }

    public function processCartItem(
        OrderItemInterface $cartItem,
        int $itemPrice,
        int $itemRetailPrice,
        int $itemDiscountPrice,
        int $itemDiscount,
        array $context
    ): void {
        /**
         * @var \CoreShop\Component\Core\Model\OrderItemInterface $cartItem
         */
        Assert::isInstanceOf($cartItem, \CoreShop\Component\Core\Model\OrderItemInterface::class);

        $product = $cartItem->getProduct();
        $cart = $context['cart'];
        $store = $context['store'];

        $taxCalculator = $this->taxCalculator->getTaxCalculator(
            $product, $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart)
        );

        $quantity = $cartItem->getQuantity();

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            if ($store->getUseGrossPrice()) {
                $totalTaxAmount = $taxCalculator->getTaxesAmountFromGross((int)round($itemPrice * $quantity));
                $itemPriceTax = $taxCalculator->getTaxesAmountFromGross($itemPrice);
                $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmountFromGross($itemRetailPrice);
                $itemDiscountTax = $taxCalculator->getTaxesAmountFromGross($itemDiscount);
                $itemDiscountPriceTax = $taxCalculator->getTaxesAmountFromGross($itemDiscountPrice);

                $splitTaxes = $this->integerDistributor->distribute($totalTaxAmount, (int)$quantity);

                $i = 0;

                foreach ($cartItem->getUnits() as $unit) {
                    if (0 === $splitTaxes[$i]) {
                        continue;
                    }

                    $unit->setSubtotal($itemPrice, true);
                    $unit->setSubtotal($itemPrice - $splitTaxes[$i], false);

                    $unit->setTotal($itemPrice, true);
                    $unit->setTotal($itemPrice - $splitTaxes[$i], false);

                    $i++;
                }

                $cartItem->setTotal((int)round($itemPrice * $quantity), true);
                $cartItem->setTotal($cartItem->getTotal(true) - $totalTaxAmount, false);

                $cartItem->setItemPrice($itemPrice, true);
                $cartItem->setItemPrice($itemPrice - $itemPriceTax, false);

                $cartItem->setItemRetailPrice($itemRetailPrice, true);
                $cartItem->setItemRetailPrice($itemRetailPrice - $itemRetailPriceTaxAmount, false);

                $cartItem->setItemDiscountPrice($itemDiscountPrice, true);
                $cartItem->setItemDiscountPrice($itemDiscountPrice - $itemDiscountPriceTax, false);

                $cartItem->setItemDiscount($itemDiscount, true);
                $cartItem->setItemDiscount($itemDiscount - $itemDiscountTax, false);
            } else {
                $totalTaxAmount = $taxCalculator->getTaxesAmount((int)round($itemPrice * $quantity));
                $itemPriceTax = $taxCalculator->getTaxesAmount($itemPrice);
                $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmount($itemRetailPrice);
                $itemDiscountTax = $taxCalculator->getTaxesAmount($itemDiscount);
                $itemDiscountPriceTax = $taxCalculator->getTaxesAmount($itemDiscountPrice);

                $splitTaxes = $this->integerDistributor->distribute($totalTaxAmount, (int)$quantity);

                $i = 0;

                foreach ($cartItem->getUnits() as $unit) {
                    if (0 === $splitTaxes[$i]) {
                        continue;
                    }

                    $unit->setSubtotal($itemPrice + $splitTaxes[$i], true);
                    $unit->setSubtotal($itemPrice, false);

                    $unit->setTotal($itemPrice + $splitTaxes[$i], true);
                    $unit->setTotal($itemPrice, false);

                    $i++;
                }

                $cartItem->setTotal((int)round($itemPrice * $quantity), false);
                $cartItem->setTotal((int)round($itemPrice * $quantity) + $totalTaxAmount, true);

                $cartItem->setItemPrice($itemPrice, false);
                $cartItem->setItemPrice($itemPrice + $itemPriceTax, true);

                $cartItem->setItemRetailPrice($itemRetailPrice, false);
                $cartItem->setItemRetailPrice($itemRetailPrice + $itemRetailPriceTaxAmount, true);

                $cartItem->setItemDiscountPrice($itemDiscountPrice, false);
                $cartItem->setItemDiscountPrice($itemDiscountPrice + $itemDiscountPriceTax, true);

                $cartItem->setItemDiscount($itemDiscount, false);
                $cartItem->setItemDiscount($itemDiscount + $itemDiscountTax, true);
            }
        } else {
            $cartItem->setTotal((int)round($itemPrice * $quantity), false);
            $cartItem->setTotal((int)round($itemPrice * $quantity), true);

            foreach ($cartItem->getUnits() as $unit) {
                $unit->setSubtotal($itemPrice, true);
                $unit->setSubtotal($itemPrice, false);

                $unit->setTotal($itemPrice, true);
                $unit->setTotal($itemPrice, false);
            }

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
             * @var \CoreShop\Component\Core\Model\OrderItemInterface $cartItem
             */
            $cartItem->setDigitalProduct($product->getDigitalProduct());
        }
    }
}
