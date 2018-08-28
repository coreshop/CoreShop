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
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartItemProcessor implements CartProcessorInterface
{
    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculator;

    /**
     * @param TaxedProductPriceCalculatorInterface $productPriceCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculator
     */
    public function __construct(
        TaxedProductPriceCalculatorInterface $productPriceCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculator
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->taxCalculator = $taxCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        /**
         * @var $item CartItemInterface
         */
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            $taxCalculator = $this->taxCalculator->getTaxCalculator($product);

            $itemNetPrice = $this->productPriceCalculator->getPrice($product, false);
            $itemGrossPrice = $this->productPriceCalculator->getPrice($product, true);

            if ($cart->getStore()->getUseGrossPrice()) {
                $totalTaxAmount = $taxCalculator->getTaxesAmountFromGross($itemGrossPrice * $item->getQuantity());

                $item->setTotal($itemGrossPrice * $item->getQuantity(), true);
                $item->setTotal($item->getTotal(true) - $totalTaxAmount, false);
            }
            else {
                $totalTaxAmount = $taxCalculator->getTaxesAmount($itemNetPrice * $item->getQuantity());

                $item->setTotal($itemNetPrice * $item->getQuantity(), false);
                $item->setTotal($itemNetPrice * $item->getQuantity() + $totalTaxAmount, true);
            }


            $item->setItemPrice($itemNetPrice, false);
            $item->setItemPrice($itemGrossPrice, true);
            //$item->setTotal($itemNetPrice * $item->getQuantity() + $totalTaxAmount, true);
            $item->setItemRetailPrice($this->productPriceCalculator->getRetailPrice($product, false), false);
            $item->setItemRetailPrice($this->productPriceCalculator->getRetailPrice($product, true), true);
            $item->setItemDiscountPrice($this->productPriceCalculator->getDiscountPrice($product, false), false);
            $item->setItemDiscountPrice($this->productPriceCalculator->getDiscountPrice($product, true), true);
            $item->setItemDiscount($this->productPriceCalculator->getDiscount($product, false), false);
            $item->setItemDiscount($this->productPriceCalculator->getDiscount($product, true), true);
            $item->setItemWholesalePrice($product->getWholesalePrice());

            if ($product instanceof ProductInterface) {
                $item->setDigitalProduct($product->getDigitalProduct());
            }
        }
    }
}