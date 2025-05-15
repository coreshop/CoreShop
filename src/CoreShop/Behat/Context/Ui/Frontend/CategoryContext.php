<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\CategoryPageInterface;
use CoreShop\Behat\Page\Frontend\HomePageInterface;
use Webmozart\Assert\Assert;

final class CategoryContext implements Context
{
    public function __construct(
        private HomePageInterface $homePage,
        private CategoryPageInterface $categoryPage,
    ) {
    }

    /**
     * @When /^I switch to category "([^"]+)" on left menu$/
     */
    public function iOpenCategoryLeft($name): void
    {
        $this->homePage->switchToCategoryOnMenuLeft($name);
    }

    /**
     * @When /^I switch to category "([^"]+)" on main menu$/
     */
    public function iOpenCategoryMain($name): void
    {
        $this->homePage->switchToCategoryOnMenuLeft($name);
    }

    /**
     * @Then I should see :numberOfProducts products in the category list
     */
    public function iShouldSeeProductsInTheCategoryList(int $numberOfProducts): void
    {
        Assert::same(count($this->categoryPage->getProductsInCategory()), $numberOfProducts);
    }

    /**
     * @Then /^I switch to view to "([^"]+)"$/
     */
    public function iSwitchView($name): void
    {
        $this->categoryPage->switchView($name);
    }

    /**
     * @Then I should see :numberOfProducts products in the category grid
     */
    public function iShouldSeeProductsInTheCategoryGrid(int $numberOfProducts): void
    {
        Assert::same(count($this->categoryPage->getProductsInCategoryGrid()), $numberOfProducts);
    }

    /**
     * @Then /^I change order to "([^"]+)"$/
     */
    public function changeOrder($order): void
    {
        $this->categoryPage->changeOrder($order);
    }

    /**
     * @Then /^I should see products in order "([^"]+)" in list$/
     */
    public function iShouldNamesInList(string $products): void
    {
        $product = explode(',', $products);
        Assert::same($this->categoryPage->getProductsInCategory(), $product);
    }

    /**
     * @Then /^I should see products in order "([^"]+)" in grid$/
     */
    public function iShouldNamesInGrid(string $products): void
    {
        $product = explode(',', $products);
        Assert::same($this->categoryPage->getProductsInCategoryGrid(), $product);
    }

    /**
     * @Then /^I should see a filter with label in order "([^"]+)" in grid$/
     */
    public function iShouldSeeFilter(string $products): void
    {
        $product = explode(',', $products);
        Assert::same($this->categoryPage->getProductsInCategoryGrid(), $product);
    }
}
