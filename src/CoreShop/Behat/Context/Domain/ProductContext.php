<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ShopperContextInterface $shopperContext;
    private ProductPriceCalculatorInterface $productPriceCalculator;
    private TaxedProductPriceCalculatorInterface $taxedProductPriceCalculator;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShopperContextInterface $shopperContext,
        ProductPriceCalculatorInterface $productPriceCalculator,
        TaxedProductPriceCalculatorInterface $taxedProductPriceCalculator
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shopperContext = $shopperContext;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->taxedProductPriceCalculator = $taxedProductPriceCalculator;
    }

    /**
     * @Then /^the (product "[^"]+") should be priced at "([^"]+)"$/
     * @Then /^the (product) should be priced at "([^"]+)"$/
     * @Then /^the (variant) should be priced at "([^"]+)"$/
     * @Then /^the (version) should be priced at "([^"]+)"$/
     */
    public function productShouldBePriced(ProductInterface $product, int $price)
    {
        Assert::same((int) $price, $this->productPriceCalculator->getPrice($product, $this->shopperContext->getContext(), true));
    }

    /**
     * @Then /^the (product "[^"]+") discount-price should be "([^"]+)"$/
     * @Then /^the (product) discount-price should be "([^"]+)"$/
     * @Then /^the (variant) discount-price should be "([^"]+)"$/
     */
    public function productsDiscountPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same((int) $price, $this->productPriceCalculator->getDiscountPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") retail-price should be "([^"]+)"$/
     * @Then /^the (product) retail-price should be "([^"]+)"$/
     * @Then /^the (variant) retail-price should be "([^"]+)"$/
     */
    public function productsRetailPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same((int) $price, $this->productPriceCalculator->getRetailPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") discount should be "([^"]+)"$/
     * @Then /^the (product) discount should be "([^"]+)"$/
     * @Then /^the (variant) discount should be "([^"]+)"$/
     */
    public function productDiscountShouldBe(ProductInterface $product, int $discount)
    {
        $productPrice = $this->productPriceCalculator->getPrice($product, $this->shopperContext->getContext(), false);
        $productDiscount = $this->productPriceCalculator->getDiscount($product, $this->shopperContext->getContext(), $productPrice);

        Assert::same($discount, $productDiscount);
    }

    /**
     * @Then /^the (product "[^"]+") should have the prices, price: "([^"]+)" and discount-price: "([^"]+)" and retail-price: "([^"]+)" and discount: "([^"]+)"$/
     * @Then /^the (product) should have the prices, price: "([^"]+)" and discount-price: "([^"]+)" and retail-price: "([^"]+)" and discount: "([^"]+)"$/
     * @Then /^the (variant) should have the prices, price: "([^"]+)" and discount-price: "([^"]+)" and retail-price: "([^"]+)" and discount: "([^"]+)"$/
     */
    public function productPricesShouldBe(ProductInterface $product, int $price, int $discountPrice, int $retailPrice, int $discount)
    {
        $this->productShouldBePriced($product, $price);
        $this->productsDiscountPriceShouldBe($product, $discountPrice);
        $this->productsRetailPriceShouldBe($product, $retailPrice);
        $this->productDiscountShouldBe($product, $discount);
    }

    /**
     * @Then /^the (product "[^"]+") should have the prices, price: "([^"]+)" and retail-price: "([^"]+)" and discount: "([^"]+)"$/
     * @Then /^the (product) should have the prices, price: "([^"]+)" and retail-price: "([^"]+)" and discount: "([^"]+)"$/
     * @Then /^the (variant) should have the prices, price: "([^"]+)" and retail-price: "([^"]+)" and discount: "([^"]+)"$/
     */
    public function productPricesShouldBeExceptDiscountPrice(ProductInterface $product, int $price, int $retailPrice, int $discount)
    {
        $this->productShouldBePriced($product, $price);
        $this->productsRetailPriceShouldBe($product, $retailPrice);
        $this->productDiscountShouldBe($product, $discount);
    }

    /**
     * @Then /^the (product "[^"]+") should be priced at "([^"]+)" including tax$/
     * @Then /^the (product) should be priced at "([^"]+)" including tax$/
     * @Then /^the (variant) should be priced at "([^"]+)" including tax$/
     */
    public function productTaxedPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same((int) $price, $this->taxedProductPriceCalculator->getPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") retail-price should be "([^"]+)" including tax$/
     * @Then /^the (product) retail-price should be "([^"]+)" including tax$/
     * @Then /^the (variant) retail-price should be "([^"]+)" including tax$/
     */
    public function productTaxedRetailPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same((int) $price, $this->taxedProductPriceCalculator->getRetailPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") should have (tax rule group "[^"]+")$/
     * @Then /^the (product) should have (tax rule group "[^"]+")$/
     * @Then /^the (variant) should have (tax rule group "[^"]+")$/
     */
    public function theProductShouldHaveTaxRuleGroup(ProductInterface $product, TaxRuleGroupInterface $taxRuleGroup)
    {
        Assert::eq($product->getTaxRule()->getId(), $taxRuleGroup->getId());
    }

    /**
     * @Then /^the (products "[^"]+") default unit should be (unit "[^"]+")$/
     * @Then /^the (products) default unit should be (unit "[^"]+")$/
     * @Then /^the (variants) default unit should be (unit "[^"]+")$/
     */
    public function theProductsDefaultUnitShouldBe(ProductInterface $product, ProductUnitInterface $unit)
    {
        $unitDefinitions = $product->getUnitDefinitions();

        Assert::notNull($unitDefinitions, 'Expected the products UnitDefinitions not to be null');

        $defaultUnitDefinition = $unitDefinitions->getDefaultUnitDefinition();

        Assert::eq(
            $defaultUnitDefinition->getUnit(),
            $unit,
            sprintf(
                'Expected the products default unit to be %s, but got %s',
                $unit->getName(),
                $defaultUnitDefinition->getUnitName()
            )
        );
    }

    /**
     * @Then /^the (product "[^"]+") should have and additional (unit "[^"]+") with conversion rate ("[^"]+")$/
     * @Then /^the (product) should have and additional (unit "[^"]+") with conversion rate ("[^"]+")$/
     * @Then /^the (variant) should have and additional (unit "[^"]+") with conversion rate ("[^"]+")$/
     */
    public function theProductsShouldHaveAnAdditionalUnitWithConversionRate(ProductInterface $product, ProductUnitInterface $unit, $conversionRate)
    {
        $unitDefinitions = $product->getUnitDefinitions();

        Assert::notNull($unitDefinitions, 'Expected the products UnitDefinitions not to be null');

        $additionalUnitDefinitions = $unitDefinitions->getAdditionalUnitDefinitions();
        $found = false;

        foreach ($additionalUnitDefinitions as $unitDefinition) {
            if ($unitDefinition->getUnit() === $unit && (float) $conversionRate === $unitDefinition->getConversionRate()) {
                $found = true;
            }
        }

        Assert::true(
            $found,
            sprintf(
                'Expected the product to have an additional unit %s with conversion-rate %s',
                $unit->getName(),
                $conversionRate
            )
        );
    }

    /**
     * @Then /^the (product) and the (copied-object) should have it's own price$/
     */
    public function bothProductsShouldHaveItsOwnPrice(ProductInterface $originalProduct, ProductInterface $copiedObject)
    {
        $originalProduct->save();
        $copiedObject->save();

        $storeValues = $originalProduct->getStoreValues();

        foreach ($storeValues as $storeValue) {
            if (!$storeValue instanceof ProductStoreValuesInterface) {
                continue;
            }

            Assert::eq($storeValue->getProduct()->getId(), $originalProduct->getId());
        }

        $copiedStoreValues = $copiedObject->getStoreValues();

        foreach ($copiedStoreValues as $copiedStoreValue) {
            if (!$copiedStoreValue instanceof ProductStoreValuesInterface) {
                continue;
            }

            Assert::eq($copiedStoreValue->getProduct()->getId(), $copiedObject->getId());
        }
    }
}
