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

use CoreShop\Component\Order\Calculator\PurchasableDiscountCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class TaxedProductPriceCalculator implements TaxedProductPriceCalculatorInterface
{
    /**
     * @var PurchasablePriceCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @var PurchasableDiscountCalculatorInterface
     */
    private $discountCalculator;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param PurchasablePriceCalculatorInterface $priceCalculator
     * @param PurchasableDiscountCalculatorInterface $discountCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        PurchasablePriceCalculatorInterface $priceCalculator,
        PurchasableDiscountCalculatorInterface $discountCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        StoreContextInterface $storeContext)
    {
        $this->priceCalculator = $priceCalculator;
        $this->discountCalculator = $discountCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, $withTax = true)
    {
        $price = $this->priceCalculator->getPrice($product);
        $discount = $this->discountCalculator->getDiscount($product, $price);

        if (is_null($price)) {
            throw new \InvalidArgumentException(sprintf("Could not determine a price for Product (%s)", $product->getId()));
        }

        $price = $price - $discount;

        $useGrossPrice = $this->storeContext->getStore()->getUseGrossPrice();

        if ($useGrossPrice) {
            if ($withTax) {
                return $price;
            }

            return $this->removeTaxes($product, $price);
        }

        if ($withTax) {
            return $this->applyTaxes($product, $price);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $product, $withTax = true)
    {
        $price = $this->getRetailPrice($product, false);

        if (is_null($price)) {
            throw new \InvalidArgumentException(sprintf("Could not determine a price for Product (%s)", $product->getId()));
        }

        $discount = $this->discountCalculator->getDiscount($product, $price);

        if ($withTax) {
            return $this->applyTaxes($product, $discount);
        }

        return $discount;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $product, $withTax = true)
    {
        $price = $this->priceCalculator->getPrice($product);

        if (is_null($price)) {
            throw new \InvalidArgumentException(sprintf("Could not determine a price for Product (%s)", $product->getId()));
        }

        $useGrossPrice = $this->storeContext->getStore()->getUseGrossPrice();

        if ($useGrossPrice) {
            if ($withTax) {
                return $price;
            }

            return $this->removeTaxes($product, $price);
        }

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
    private function applyTaxes(PurchasableInterface $product, $price)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->applyTaxes($price);
        }

        return $price;
    }

    /**
     * @param PurchasableInterface $product
     * @param $price
     * @return int
     */
    private function removeTaxes(PurchasableInterface $product, $price)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->removeTaxes($price);
        }

        return $price;
    }
}
