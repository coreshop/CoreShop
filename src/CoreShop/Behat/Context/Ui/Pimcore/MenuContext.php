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

namespace CoreShop\Behat\Context\Ui\Pimcore;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Element\Pimcore\MenuElementInterface;
use Webmozart\Assert\Assert;

final class MenuContext implements Context
{
    public function __construct(
        private MenuElementInterface $menuElement,
    ) {
    }

    /**
     * @When There should be a menu File
     */
    public function thereShouldBeAMenuFile(): void
    {
        Assert::true($this->menuElement->hasMenuWithIdentifier('pimcore_menu_file'));
    }

    /**
     * @When There should be a menu Tools
     */
    public function thereShouldBeAMenuTools(): void
    {
        Assert::true($this->menuElement->hasMenuWithIdentifier('pimcore_menu_extras'));
    }

    /**
     * @When There should be a menu Marketing
     */
    public function thereShouldBeAMenuMarketing(): void
    {
        Assert::true($this->menuElement->hasMenuWithIdentifier('pimcore_menu_marketing'));
    }

    /**
     * @When There should be a menu Settings
     */
    public function thereShouldBeAMenuSettings(): void
    {
        Assert::true($this->menuElement->hasMenuWithIdentifier('pimcore_menu_settings'));
    }

    /**
     * @When There should be a menu Search
     */
    public function thereShouldBeAMenuSearch(): void
    {
        Assert::true($this->menuElement->hasMenuWithIdentifier('pimcore_menu_search'));
    }

    /**
     * @When There should be a menu CoreShop
     */
    public function thereShouldBeAMenuCoreShop(): void
    {
        Assert::true($this->menuElement->hasMenuWithIdentifier('pimcore_menu_coreshop_main'));
    }

    /**
     * @Given I open the CoreShop menu
     */
    public function IOpenTheCoreShopMenu(): void
    {
        $this->menuElement->openMenuWithIdentifier('pimcore_menu_coreshop_main');
    }

    /**
     * @Then a Menu should be open
     */
    public function aMenuShouldBeOpened(): void
    {
        Assert::true($this->menuElement->aMenuIsOpen());
    }

    /**
     * @Then the opened Menu should have :count items
     */
    public function theOpenedMenuShouldHaveCountItems(int $count): void
    {
        Assert::true($this->menuElement->openMenuHasItems($count));
    }

    /**
     * @Given /^I hover over the Menu Item with Name "([^"]+)"$/
     */
    public function iHoverOverTheMenuItemWithName(string $name): void
    {
        $this->menuElement->hoverOverItemWithName($name);
    }

    /**
     * @Given Two Menus should be opened
     */
    public function twoMenusShouldBeOpened(): void
    {
        Assert::true($this->menuElement->twoMenusShouldBeOpen());
    }
}
