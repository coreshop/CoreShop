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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductPriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    private $taxedProductPriceCalculator;

    /**
     * @param SharedStorageInterface               $sharedStorage
     * @param ShopperContextInterface              $shopperContext,
     * @param ProductRepositoryInterface           $productRepository
     * @param ProductPriceCalculatorInterface      $productPriceCalculator
     * @param TaxedProductPriceCalculatorInterface $taxedProductPriceCalculator
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShopperContextInterface $shopperContext,
        ProductRepositoryInterface $productRepository,
        ProductPriceCalculatorInterface $productPriceCalculator,
        TaxedProductPriceCalculatorInterface $taxedProductPriceCalculator
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shopperContext = $shopperContext;
        $this->productRepository = $productRepository;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->taxedProductPriceCalculator = $taxedProductPriceCalculator;
    }

    /**
     * @Then /^the (product "[^"]+") should be priced at "([^"]+)"$/
     */
    public function productShouldBePriced(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->productPriceCalculator->getPrice($product, $this->shopperContext->getContext(), true));
    }

    /**
     * @Then /^the (product "[^"]+") discount-price should be "([^"]+)"$/
     */
    public function productsDiscountPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->productPriceCalculator->getDiscountPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") retail-price should be "([^"]+)"$/
     */
    public function productsRetailPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->productPriceCalculator->getRetailPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") discount should be "([^"]+)"$/
     */
    public function productDiscountShouldBe(ProductInterface $product, int $discount)
    {
        $productPrice = $this->productPriceCalculator->getPrice($product, $this->shopperContext->getContext(), false);
        $productDiscount = $this->productPriceCalculator->getDiscount($product, $this->shopperContext->getContext(), $productPrice);

        Assert::same($discount, $productDiscount);
    }

    /**
     * @Then /^the (product "[^"]+") should have the prices, price: "([^"]+)" and discount-price: "([^"]+)" and retail-price: "([^"]+)" and discount: "([^"]+)"$/
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
     */
    public function productPricesShouldBeExceptDiscountPrice(ProductInterface $product, int $price, int $retailPrice, int $discount)
    {
        $this->productShouldBePriced($product, $price);
        $this->productsRetailPriceShouldBe($product, $retailPrice);
        $this->productDiscountShouldBe($product, $discount);
    }

    /**
     * @Then /^the (product "[^"]+") should be priced at "([^"]+)" including tax$/
     */
    public function productTaxedPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->taxedProductPriceCalculator->getPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") retail-price should be "([^"]+)" including tax$/
     */
    public function productTaxedRetailPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->taxedProductPriceCalculator->getRetailPrice($product, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (product "[^"]+") should have (tax rule group "[^"]+")$/
     */
    public function theProductShouldHaveTaxRuleGroup(ProductInterface $product, TaxRuleGroupInterface $taxRuleGroup)
    {
        Assert::eq($product->getTaxRule()->getId(), $taxRuleGroup->getId());
    }
}
