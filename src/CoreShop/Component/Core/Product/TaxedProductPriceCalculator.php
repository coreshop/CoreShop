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

namespace CoreShop\Component\Core\Product;


use CoreShop\Component\Core\Model\ProductInterface;


use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class TaxedProductPriceCalculator implements TaxedProductPriceCalculatorInterface
{
    /**
     * @var ProductPriceCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @param ProductPriceCalculatorInterface $priceCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(ProductPriceCalculatorInterface $priceCalculator, ProductTaxCalculatorFactoryInterface $taxCalculatorFactory)
    {
        $this->priceCalculator = $priceCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, $withTax = true) {
        if ($product instanceof ProductInterface) {
            $price = $this->priceCalculator->getPrice($product);
            $discount = $this->priceCalculator->getDiscount($product, $price);

            $price = $price - $discount;
        }
        else {
            $price = $product->getPrice();
        }

        if ($withTax) {
            return $this->applyTaxes($product, $price);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $product, $withTax = true) {
        if ($product instanceof ProductInterface) {
            $price = $this->priceCalculator->getPrice($product);
            $discount = $this->priceCalculator->getDiscount($product, $price);
        }
        else {
            $discount = 0;
        }

        if ($withTax) {
            return $this->applyTaxes($product, $discount);
        }

        return $discount;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $product, $withTax = true) {
        $price = $product->getPrice();

        if ($withTax) {
            return $this->applyTaxes($product, $price);
        }

        return $price;
    }

    /**
     * @param PurchasableInterface $product
     * @param $price
     * @return int
     */
    private function applyTaxes(PurchasableInterface $product, $price) {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->applyTaxes($price);
        }

        return $price;
    }
}
