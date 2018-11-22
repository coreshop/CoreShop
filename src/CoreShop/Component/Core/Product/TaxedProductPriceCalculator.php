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

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Provider\DefaultTaxAddressProviderInterface;
use CoreShop\Component\Core\Taxation\TaxApplicatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class TaxedProductPriceCalculator implements TaxedProductPriceCalculatorInterface
{
    /**
     * @var PurchasableCalculatorInterface
     */
    private $purchasableCalculator;

    /**
     * @var DefaultTaxAddressProviderInterface
     */
    private $defaultTaxAddressProvider;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var TaxApplicatorInterface
     */
    private $taxApplicator;

    /**
     * @param PurchasableCalculatorInterface       $purchasableCalculator
     * @param DefaultTaxAddressProviderInterface   $defaultTaxAddressProvider
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param TaxApplicatorInterface               $taxApplicator
     */
    public function __construct(
        PurchasableCalculatorInterface $purchasableCalculator,
        DefaultTaxAddressProviderInterface $defaultTaxAddressProvider,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        TaxApplicatorInterface $taxApplicator
    ) {
        $this->purchasableCalculator = $purchasableCalculator;
        $this->defaultTaxAddressProvider = $defaultTaxAddressProvider;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->taxApplicator = $taxApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, array $context, $withTax = true)
    {
        $price = $this->purchasableCalculator->getPrice($product, $context, true);
        $taxCalculator = $this->getTaxCalculator($product, $context);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($price, $context, $taxCalculator, $withTax);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $product, array $context, $withTax = true)
    {
        $price = $this->purchasableCalculator->getDiscountPrice($product, $context);

        if (is_null($price)) {
            throw new \InvalidArgumentException(
                sprintf("Could not determine a discount price for Product (%s)", $product->getId())
            );
        }

        $taxCalculator = $this->getTaxCalculator($product, $context);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($price, $context, $taxCalculator, $withTax);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $product, array $context, $withTax = true)
    {
        $price = $this->purchasableCalculator->getPrice($product, $context);
        $discount = $this->purchasableCalculator->getDiscount($product, $context, $price);
        $taxCalculator = $this->getTaxCalculator($product, $context);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($discount, $context, $taxCalculator, $withTax);
        }

        return $discount;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $product, array $context, $withTax = true)
    {
        $price = $this->purchasableCalculator->getRetailPrice($product, $context);

        if (is_null($price)) {
            throw new \InvalidArgumentException(
                sprintf("Could not determine a price for Product (%s)", $product->getId())
            );
        }

        $taxCalculator = $this->getTaxCalculator($product, $context);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($price, $context, $taxCalculator, $withTax);
        }

        return $price;
    }

    /**
     * @param PurchasableInterface $product
     * @param array                $context
     * @return TaxCalculatorInterface
     */
    protected function getTaxCalculator(PurchasableInterface $product, array $context)
    {
        return $this->taxCalculatorFactory->getTaxCalculator($product, $this->getDefaultAddress($context));
    }

    /**
     * @param array $context
     * @return AddressInterface|null
     */
    protected function getDefaultAddress($context)
    {
        return $this->defaultTaxAddressProvider->getAddress($context);
    }
}
