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

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class TaxedProductPriceCalculator implements TaxedProductPriceCalculatorInterface
{
    /**
     * @var ProductPriceCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var PimcoreFactoryInterface
     */
    private $addressFactory;

    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @param ProductPriceCalculatorInterface $priceCalculator
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param PimcoreFactoryInterface $addressFactory
     * @param CountryContextInterface $countryContext
     */
    public function __construct(ProductPriceCalculatorInterface $priceCalculator, TaxCalculatorFactoryInterface $taxCalculatorFactory, PimcoreFactoryInterface $addressFactory, CountryContextInterface $countryContext)
    {
        $this->priceCalculator = $priceCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->addressFactory = $addressFactory;
        $this->countryContext = $countryContext;
    }

    /**
     * @param $taxRuleGroup
     * @return \CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface|null
     */
    private function getTaxCalculator($taxRuleGroup) {
        if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
            $address = $this->addressFactory->createNew();
            $country = $this->countryContext->getCountry();

            $address->setCountry($country);

            return $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, $withTax = true) {
        $price = $this->priceCalculator->getPrice($product);
        $discount = $this->priceCalculator->getDiscount($product, $price);
        
        $price = $price - $discount;

        if ($withTax) {
            return $this->applyTaxes($product, $price);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $product, $withTax = true) {
        $price = $product->getBasePrice();

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
        $taxCalculator = $this->getTaxCalculator($product->getTaxRule());

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->applyTaxes($price);
        }

        return $price;
    }
}
