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

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\HomePageInterface;
use CoreShop\Behat\Page\Frontend\ProductPageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    private $sharedStorage;
    private $linkGenerator;
    private $productPage;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        LinkGeneratorInterface $linkGenerator,
        ProductPageInterface $productPage
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->linkGenerator = $linkGenerator;
        $this->productPage = $productPage;
    }

    /**
     * @When /^I open the page "([^"]+)" for this (product)$/
     */
    public function iOpenPage($url, ProductInterface $product)
    {
        $url = str_replace('%id%', $product->getId(), $url);

        $this->productPage->tryToOpenWithUri($url);
    }

    /**
     * @Then /^I should be on the (product's) detail page$/
     */
    public function iShouldBeOnProductDetailedPage(ProductInterface $product)
    {
        Assert::true($this->productPage->isOpenWithUri($this->linkGenerator->generate($product, null, ['_locale' => 'en'])));
    }

    /**
     * @When /^I open the (product's) detail page$/
     */
    public function iCheckLatestProducts(ProductInterface $product): void
    {
        $this->productPage->tryToOpenWithUri($this->linkGenerator->generate($product, null, ['_locale' => 'en']));
    }

    /**
     * @Then I should see the product name :name
     */
    public function iShouldSeeProductName($name)
    {
        Assert::same($this->productPage->getName(), $name);
    }

    /**
     * @Then I should see the price :price
     */
    public function iShouldSeeThePrice($price)
    {
        Assert::same($this->productPage->getPrice(), $price);
    }

    /**
     * @Then I should see the original price :price
     */
    public function iShouldSeeTheOriginalPrice($price)
    {
        Assert::same($this->productPage->getOriginalPrice(), $price);
    }

    /**
     * @Then I should see the discount of :price
     */
    public function iShouldSeeTheDiscountOf($discount)
    {
        Assert::same($this->productPage->getDiscount(), $discount);
    }

    /**
     * @Then I should see :taxRate tax-rate
     */
    public function iShouldSeeTheTaxRate($taxRate)
    {
        Assert::same($this->productPage->getTaxRate(), $taxRate);
    }

    /**
     * @Then I should see :tax tax
     */
    public function iShouldSeeTheTax($tax)
    {
        Assert::same($this->productPage->getTax(), $tax);
    }

    /**
     * @Then I should see the price :price for unit :unit
     */
    public function iShouldSeeThePriceForUnit($price, $unit)
    {
        Assert::same($this->productPage->getPriceForUnit($unit), $price);
    }

    /**
     * @Then I should see one quantity price rule with price :price
     */
    public function iShouldSeeOneQuantityPiceRuleWithPrice($price)
    {
        $priceRules = $this->productPage->getQuantityPriceRules();

        Assert::count($priceRules, 1);
        Assert::contains($priceRules[0]['price'], $price);
    }

    /**
     * @Then I should see the quantity price rule :number with price :price
     */
    public function iShouldSeeTheQuantityPriceRuleWithPrice(int $number, $price)
    {
        $number--;
        $priceRules = $this->productPage->getQuantityPriceRules();

        Assert::greaterThan($priceRules, $number+1);
        Assert::contains($priceRules[$number]['price'], $price);
    }

    /**
     * @Then I should see the quantity price rule :number with excl price :price
     */
    public function iShouldSeeTheQuantityPriceRuleWithInclPrice(int $number, $price)
    {
        $number--;
        $priceRules = $this->productPage->getQuantityPriceRules();

        Assert::greaterThan($priceRules, $number+1);
        Assert::contains($priceRules[$number]['priceExcl'], $price);
    }

    /**
     * @Then I should see the quantity price rule :number starting from :startingFrom
     */
    public function iShouldSeeTheQuantityPriceRuleStartingFrom(int $number, $startingFrom)
    {
        $number--;
        $priceRules = $this->productPage->getQuantityPriceRules();

        Assert::greaterThan($priceRules, $number+1);
        Assert::contains($priceRules[$number]['startingFrom'], $startingFrom);
    }


    /**
     * @Then /^I should see one quantity price rule with price "([^"]+)" for (unit "[^"]+")$/
     */
    public function iShouldSeeOneQuantityPiceRuleForUnitWithPrice($price, ProductUnitInterface $unit)
    {
        $priceRules = $this->productPage->getQuantityPriceRulesForUnit($unit);

        Assert::count($priceRules, 1);
        Assert::contains($priceRules[0]['price'], $price);
    }

    /**
     * @Then /^I should see the quantity price rule (\d+) with price "([^"]+)" for (unit "[^"]+")$/
     */
    public function iShouldSeeTheQuantityPriceRuleForUnitWithPrice(int $number, $price, ProductUnitInterface $unit)
    {
        $number--;
        $priceRules = $this->productPage->getQuantityPriceRulesForUnit($unit);

        Assert::greaterThan($priceRules, $number+1);
        Assert::contains($priceRules[$number]['price'], $price);
    }

    /**
     * @Then /^I should see the quantity price rule (\d+) with excl price "([^"]+)" for (unit "[^"]+")$/
     */
    public function iShouldSeeTheQuantityPriceRuleForUnitWithInclPrice(int $number, $price, ProductUnitInterface $unit)
    {
        $number--;
        $priceRules = $this->productPage->getQuantityPriceRulesForUnit($unit);

        Assert::greaterThan($priceRules, $number+1);
        Assert::contains($priceRules[$number]['priceExcl'], $price);
    }

    /**
     * @Then /^I should see the quantity price rule (\d+) starting from "(\d+)" for (unit "[^"]+")$/
     */
    public function iShouldSeeTheQuantityPriceRuleForUnitStartingFrom(int $number, $startingFrom, ProductUnitInterface $unit)
    {
        $number--;
        $priceRules = $this->productPage->getQuantityPriceRulesForUnit($unit);

        Assert::greaterThan($priceRules, $number+1);
        Assert::contains($priceRules[$number]['startingFrom'], $startingFrom);
    }
    /**
     * @Then /^I should see that this (product) is out of stock$/
     */
    public function iShouldSeeThatThisProductIsOutOfStock(ProductInterface $product)
    {
        $this->productPage->tryToOpenWithUri($this->linkGenerator->generate($product, null, ['_locale' => 'en']));

        Assert::true($this->productPage->getIsOutOfStock());
    }
}
