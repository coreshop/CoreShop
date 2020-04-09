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
use CoreShop\Behat\Page\Frontend\CartPageInterface;
use CoreShop\Behat\Page\Frontend\HomePageInterface;
use CoreShop\Behat\Page\Frontend\ProductPageInterface;
use CoreShop\Behat\Service\NotificationCheckerInterface;
use CoreShop\Behat\Service\NotificationType;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    private $sharedStorage;
    private $linkGenerator;
    private $cartPage;
    private $productPage;
    private $notificationChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        LinkGeneratorInterface $linkGenerator,
        NotificationCheckerInterface $notificationChecker,
        CartPageInterface $cartPage,
        ProductPageInterface $productPage
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->linkGenerator = $linkGenerator;
        $this->notificationChecker = $notificationChecker;
        $this->cartPage = $cartPage;
        $this->productPage = $productPage;
    }

    /**
     * @When I see the summary of my cart
     */
    public function iOpenCartSummaryPage()
    {
        $this->cartPage->open();
    }

    /**
     * @Then my cart should be empty
     * @Then cart should be empty with no value
     */
    public function iShouldBeNotifiedThatMyCartIsEmpty()
    {
        $this->cartPage->open();

        Assert::true($this->cartPage->isEmpty());
    }

    /**
     * @Given /^I add this (product) to the cart$/
     * @Given /^I add (product "[^"]+") to the cart$/
     */
    public function iAddProductToTheCart(ProductInterface $product): void
    {
        $this->productPage->tryToOpenWithUri($this->linkGenerator->generate($product, null, ['_locale' => 'en']));
        $this->productPage->addToCart();

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Given /^I add this (product) in (unit "[^"]+") to the cart$/
     * @Given /^I add (product "[^"]+") in (unit "[^"]+") to the cart$/
     */
    public function iAddProductInUnitToTheCart(ProductInterface $product, ProductUnitInterface $unit): void
    {
        $unitDefinition = $this->findUnitDefinition($product, $unit);

        $this->productPage->tryToOpenWithUri($this->linkGenerator->generate($product, null, ['_locale' => 'en']));
        $this->productPage->addToCartInUnit($unitDefinition);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Given I removed product :productName from the cart
     * @When I remove product :productName from the cart
     */
    public function iRemoveProductFromTheCart(string $productName): void
    {
        $this->cartPage->open();
        $this->cartPage->removeProduct($productName);
    }

    /**
     * @Given /^I add (\d+) of this (product) to the cart$/
     */
    public function iAddQuantityProductToTheCart($quantity, ProductInterface $product)
    {
        $this->productPage->tryToOpenWithUri($this->linkGenerator->generate($product, null, ['_locale' => 'en']));
        $this->productPage->addToCartWithQuantity($quantity);
    }

    /**
     * @Then /^I should be(?: on| redirected to) the cart summary page$/
     */
    public function shouldBeOnMyCartSummaryPage()
    {
        $this->cartPage->verify();
    }

        /**
     * @Then I should be notified that the product has been successfully added
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyAdded()
    {
        $this->notificationChecker->checkNotification('ITEM ADDED', NotificationType::success());
    }

    /**
     * @Then there should be one item in my cart
     */
    public function thereShouldBeOneItemInMyCart()
    {
        Assert::true($this->cartPage->isSingleItemOnPage());
    }

    /**
     * @Then this item should have name :itemName
     */
    public function thisProductShouldHaveName($itemName)
    {
        Assert::true($this->cartPage->hasItemNamed($itemName));
    }

    /**
     * @Then /^I should see "([^"]+)" with unit price "([^"]+)" in my cart$/
     */
    public function iShouldSeeProductWithUnitPriceInMyCart($productName, $unitPrice)
    {
        Assert::same($this->cartPage->getItemUnitPrice($productName), $unitPrice);
    }

    /**
     * @Then /^I should see "([^"]+)" with total price "([^"]+)" in my cart$/
     */
    public function iShouldSeeProductWithTotalPriceInMyCart($productName, $unitPrice)
    {
        Assert::same($this->cartPage->getItemTotalPrice($productName), $unitPrice);
    }

    /**
     * @Then /^I should see (product "[^"]+") in (unit "[^"]+") with unit price "([^"]+)" in my cart$/
     */
    public function iShouldSeeProductInUnitWithUnitPriceInMyCart(ProductInterface $product, ProductUnitInterface $unit, $unitPrice)
    {
        $unitDefinition = $this->findUnitDefinition($product, $unit);

        Assert::same($this->cartPage->getItemUnitPriceWithUnit($product->getName(), $unitDefinition), $unitPrice);
    }

    /**
     * @Then /^I should see this (product) with (unit "[^"]+") in my cart$/
     * @Then /^I should see (product "[^"]+") with (unit "[^"]+") in my cart$/
     */
    public function iShouldSeeProductWithUnitInMyCart(ProductInterface $product, ProductUnitInterface $unit)
    {
        $unitDefinition = $this->findUnitDefinition($product, $unit);

        Assert::true($this->cartPage->hasProductInUnit($product->getName(), $unitDefinition));
    }

    /**
     * @Then /^I should see "([^"]+)" with quantity (\d+) in my cart$/
     */
    public function iShouldSeeWithQuantityInMyCart($productName, $quantity)
    {
        Assert::same($this->cartPage->getQuantity($productName), (int) $quantity);
    }

    /**
     * @Given I change :productName quantity to :quantity
     */
    public function iChangeQuantityTo($productName, $quantity)
    {
        $this->cartPage->open();
        $this->cartPage->changeQuantity($productName, $quantity);
    }

    /**
     * @Then my cart's total should be :total
     */
    public function myCartsTotalShouldBe($total)
    {
        $this->cartPage->open();

        Assert::same($this->cartPage->getTotal(), $total);
    }

    protected function findUnitDefinition(ProductInterface $product, ProductUnitInterface $unit)
    {
        $unitDefinition = null;

        Assert::notNull($product->getUnitDefinitions());

        foreach ($product->getUnitDefinitions()->getUnitDefinitions() as $definition) {
            if ($definition->getUnit()->getId() === $unit->getId()) {
                $unitDefinition = $definition;
                break;
            }
        }

        Assert::notNull($unitDefinition);

        return $unitDefinition;
    }
}
