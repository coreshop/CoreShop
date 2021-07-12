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

namespace CoreShop\Behat\Context\Ui\Pimcore;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Element\Pimcore\MenuElementInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class MenuContext implements Context
{
    private PWAPageInterface $pwaPage;
    private MenuElementInterface $menuElement;

    public function __construct(
        PWAPageInterface $pwaPage,
        MenuElementInterface $menuElement
    ) {
        $this->pwaPage = $pwaPage;
        $this->menuElement = $menuElement;
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
