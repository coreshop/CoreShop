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

use CoreShop\Component\Core\Taxation\TaxApplicatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableDiscountCalculatorInterface;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
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
     * @var TaxApplicatorInterface
     */
    private $taxApplicator;

    /**
     * @param PurchasablePriceCalculatorInterface $priceCalculator
     * @param PurchasableDiscountCalculatorInterface $discountCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param TaxApplicatorInterface $taxApplicator
     */
    public function __construct(
        PurchasablePriceCalculatorInterface $priceCalculator,
        PurchasableDiscountCalculatorInterface $discountCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        TaxApplicatorInterface $taxApplicator
    )
    {
        $this->priceCalculator = $priceCalculator;
        $this->discountCalculator = $discountCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->taxApplicator = $taxApplicator;
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

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($price, $taxCalculator, $withTax);
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
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface && $withTax) {
            return $taxCalculator->applyTaxes($discount);
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

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($price, $taxCalculator, $withTax);
        }

        return $price;
    }
}
