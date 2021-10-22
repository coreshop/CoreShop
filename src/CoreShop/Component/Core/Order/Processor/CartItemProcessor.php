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
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartItemProcessorInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

final class CartItemProcessor implements CartItemProcessorInterface
{
    public function __construct(private ProductTaxCalculatorFactoryInterface $taxCalculator, private AddressProviderInterface $defaultAddressProvider, private RepositoryInterface $taxRateRepository, private FactoryInterface $taxItemFactory)
    {
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
            $product,
            $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart)
        );

        $quantity = $cartItem->getQuantity();

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            if ($store->getUseGrossPrice()) {
                $totalTaxAmount = $taxCalculator->getTaxesAmountFromGross((int)round($itemPrice * $quantity));
                $totalTaxAmountArray = $taxCalculator->getTaxesAmountFromGrossAsArray((int)round($itemPrice * $quantity));
                $itemPriceTax = $taxCalculator->getTaxesAmountFromGross($itemPrice);
                $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmountFromGross($itemRetailPrice);
                $itemDiscountTax = $taxCalculator->getTaxesAmountFromGross($itemDiscount);
                $itemDiscountPriceTax = $taxCalculator->getTaxesAmountFromGross($itemDiscountPrice);
                $taxes = $this->collectItemTaxes($totalTaxAmountArray);

                $cartItem->setTaxes(new Fieldcollection($taxes));

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
                $totalTaxAmountArray = $taxCalculator->getTaxesAmountAsArray((int)round($itemPrice * $quantity));
                $itemPriceTax = $taxCalculator->getTaxesAmount($itemPrice);
                $itemRetailPriceTaxAmount = $taxCalculator->getTaxesAmount($itemRetailPrice);
                $itemDiscountTax = $taxCalculator->getTaxesAmount($itemDiscount);
                $itemDiscountPriceTax = $taxCalculator->getTaxesAmount($itemDiscountPrice);
                $taxes = $this->collectItemTaxes($totalTaxAmountArray);

                $cartItem->setTaxes(new Fieldcollection($taxes));

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

    private function collectTaxes(int $i, array $taxes)
    {
        $usedTaxes = [];

        foreach ($taxes as $taxId => $splitted) {
            $tax = $this->taxRateRepository->find($taxId);

            if ($splitted[$i] <= 0) {
                continue;
            }

            if (!array_key_exists($tax->getId(), $usedTaxes)) {
                /**
                 * @var TaxItemInterface $item
                 */
                $item = $this->taxItemFactory->createNew();
                $item->setName($tax->getName());
                $item->setRate($tax->getRate());
                $item->setAmount($splitted[$i]);

                $usedTaxes[$tax->getId()] = $item;
            } else {
                $usedTaxes[$tax->getId()]->setAmount($usedTaxes[$tax->getId()]->getAmount() + $splitted[$i]);
            }
        }

        return $usedTaxes;
    }

    private function collectItemTaxes(array $taxes)
    {
        $usedTaxes = [];

        foreach ($taxes as $taxId => $amount) {
            $tax = $this->taxRateRepository->find($taxId);

            if ($amount <= 0) {
                continue;
            }

            if (!array_key_exists($tax->getId(), $usedTaxes)) {
                /**
                 * @var TaxItemInterface $item
                 */
                $item = $this->taxItemFactory->createNew();
                $item->setName($tax->getName());
                $item->setRate($tax->getRate());
                $item->setAmount($amount);

                $usedTaxes[$tax->getId()] = $item;
            } else {
                $usedTaxes[$tax->getId()]->setAmount($usedTaxes[$tax->getId()]->getAmount() + $amount);
            }
        }

        return $usedTaxes;
    }
}
