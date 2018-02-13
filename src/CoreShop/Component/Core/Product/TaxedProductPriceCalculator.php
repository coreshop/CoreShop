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
use CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface;
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
     * @var PurchasableDiscountPriceCalculatorInterface
     */
    private $discountPriceCalculator;

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
     * @param PurchasableDiscountPriceCalculatorInterface $discountPriceCalculator
     * @param PurchasableDiscountCalculatorInterface $discountCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param TaxApplicatorInterface $taxApplicator
     */
    public function __construct(
        PurchasablePriceCalculatorInterface $priceCalculator,
        PurchasableDiscountPriceCalculatorInterface $discountPriceCalculator,
        PurchasableDiscountCalculatorInterface $discountCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        TaxApplicatorInterface $taxApplicator
    )
    {
        $this->priceCalculator = $priceCalculator;
        $this->discountPriceCalculator = $discountPriceCalculator;
        $this->discountCalculator = $discountCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->taxApplicator = $taxApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, $withTax = true)
    {
        $retailPrice = $this->priceCalculator->getPrice($product);
        $discountPrice = $this->discountPriceCalculator->getDiscountPrice($product);

        if (is_null($retailPrice)) {
            throw new \InvalidArgumentException(sprintf("Could not determine a price for Product (%s)", $product->getId()));
        }

        if ($discountPrice > 0 && $discountPrice < $retailPrice) {
            $price = $discountPrice;
        } else {
            $price = $retailPrice;
        }

        $discount = $this->discountCalculator->getDiscount($product, $price);

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
    public function getDiscountPrice(PurchasableInterface $product, $withTax = true)
    {
        $price = $this->discountPriceCalculator->getDiscountPrice($product);

        if (is_null($price)) {
            throw new \InvalidArgumentException(sprintf("Could not determine a discount price for Product (%s)", $product->getId()));
        }

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface && $withTax) {
            return $this->taxApplicator->applyTax($price, $taxCalculator, $withTax);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $product, $withTax = true)
    {
        $price = $this->discountPriceCalculator->getDiscountPrice($product);

        if (is_null($price)) {
            throw new \InvalidArgumentException(sprintf("Could not determine a price for Product (%s)", $product->getId()));
        }

        $discount = $this->discountCalculator->getDiscount($product, $price);
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);

        if ($taxCalculator instanceof TaxCalculatorInterface && $withTax) {
            return $this->taxApplicator->applyTax($discount, $taxCalculator, $withTax);
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
