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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Webmozart\Assert\Assert;

final class CartItemProcessor implements CartProcessorInterface
{
    /**
     * @var PurchasableCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculator;

    /**
     * @var AddressProviderInterface
     */
    private $defaultAddressProvider;

    /**
     * @param PurchasableCalculatorInterface $productPriceCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculator
     * @param AddressProviderInterface $defaultAddressProvider
     */
    public function __construct(
        PurchasableCalculatorInterface $productPriceCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculator,
        AddressProviderInterface $defaultAddressProvider
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->taxCalculator = $taxCalculator;
        $this->defaultAddressProvider = $defaultAddressProvider;

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
            'cart' => $cart
        ];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            $taxCalculator = $this->taxCalculator->getTaxCalculator($product, $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart));

            $itemPrice = $this->productPriceCalculator->getPrice($product, $context,true);
            $itemPriceWithoutDiscount = $this->productPriceCalculator->getPrice($product, $context);
            $itemRetailPrice = $this->productPriceCalculator->getRetailPrice($product, $context);
            $itemDiscountPrice = $this->productPriceCalculator->getDiscountPrice($product, $context);
            $itemDiscount = $this->productPriceCalculator->getDiscount($product, $context, $itemPriceWithoutDiscount);

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                if ($store->getUseGrossPrice()) {
                    $totalTaxAmount = $taxCalculator->getTaxesAmountFromGross($itemPrice * $item->getQuantity());
                    $itemPriceTax = $taxCalculator->getTaxesAmountFromGross($itemPrice);
                    $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmountFromGross($itemRetailPrice);
                    $itemDiscountTax = $taxCalculator->getTaxesAmountFromGross($itemDiscount);
                    $itemDiscountPriceTax = $taxCalculator->getTaxesAmountFromGross($itemDiscountPrice);

                    $item->setTotal($itemPrice * $item->getQuantity(), true);
                    $item->setTotal($item->getTotal(true) - $totalTaxAmount, false);

                    $item->setItemPrice($itemPrice, true);
                    $item->setItemPrice($itemPrice - $itemPriceTax, false);

                    $item->setItemRetailPrice($itemRetailPrice, true);
                    $item->setItemRetailPrice($itemRetailPrice - $itemRetailPriceTaxAmount, false);

                    $item->setItemDiscountPrice($itemDiscountPrice, true);
                    $item->setItemDiscountPrice($itemDiscountPrice - $itemDiscountTax, false);

                    $item->setItemDiscount($itemDiscount, true);
                    $item->setItemDiscount($itemDiscount - $itemDiscountPriceTax, false);

                } else {
                    $totalTaxAmount = $taxCalculator->getTaxesAmount($itemPrice * $item->getQuantity());
                    $itemPriceTax = $taxCalculator->getTaxesAmount($itemPrice);
                    $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmount($itemRetailPrice);
                    $itemDiscountTax = $taxCalculator->getTaxesAmount($itemDiscount);
                    $itemDiscountPriceTax = $taxCalculator->getTaxesAmount($itemDiscountPrice);

                    $item->setTotal($itemPrice * $item->getQuantity(), false);
                    $item->setTotal($itemPrice * $item->getQuantity() + $totalTaxAmount, true);

                    $item->setItemPrice($itemPrice, false);
                    $item->setItemPrice($itemPrice + $itemPriceTax, true);

                    $item->setItemRetailPrice($itemRetailPrice, false);
                    $item->setItemRetailPrice($itemRetailPrice + $itemRetailPriceTaxAmount, true);

                    $item->setItemDiscountPrice($itemDiscountPrice, false);
                    $item->setItemDiscountPrice($itemDiscountPrice + $itemDiscountTax, true);

                    $item->setItemDiscount($itemDiscount, false);
                    $item->setItemDiscount($itemDiscount + $itemDiscountPriceTax, true);
                }
            }
            else {
                $item->setTotal($itemPrice * $item->getQuantity(), false);
                $item->setTotal($itemPrice * $item->getQuantity(), true);

                $item->setItemRetailPrice($itemRetailPrice, false);
                $item->setItemRetailPrice($itemRetailPrice, true);

                $item->setItemDiscountPrice($itemDiscountPrice, false);
                $item->setItemDiscountPrice($itemDiscountPrice, true);

                $item->setItemDiscount($itemDiscount, false);
                $item->setItemDiscount($itemDiscount, true);
            }
        }
    }
}