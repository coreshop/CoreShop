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
use CoreShop\Behat\Page\Pimcore\LoginPageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    private $adminLoginPage;
    private $pwaPage;

    public function __construct(
        LoginPageInterface $adminLoginPage,
        PWAPageInterface $pwaPage
    ) {
        $this->adminLoginPage = $adminLoginPage;
        $this->pwaPage = $pwaPage;
    }

    /**
     * @When I want to log into Pimcore backend
     */
    public function iWantToLogIn(): void
    {
        $this->adminLoginPage->open();
    }

    /**
     * @When I log into the Pimcore backend
     */
    public function ILogIntoPimcoreBackend(): void
    {
        $this->adminLoginPage->open();
        $this->adminLoginPage->specifyUsername('admin');
        $this->adminLoginPage->specifyPassword('coreshop');
        $this->adminLoginPage->logIn();
        $this->iShouldBeLoggedIn();
    }

    /**
     * @When I specify the username as :username
     * @When I do not specify the username
     */
    public function iSpecifyTheUsername(?string $username = null): void
    {
        $this->adminLoginPage->specifyUsername($username);
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs(?string $password = null): void
    {
        $this->adminLoginPage->specifyPassword($password);
    }

    /**
     * @When I log in
     * @When I try to log in
     */
    public function iLogIn(): void
    {
        $this->adminLoginPage->logIn();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        $this->pwaPage->waitTillLoaded();
        $this->pwaPage->verify();

        Assert::true($this->pwaPage->hasLogoutButton());
    }

    /**
     * @When I open Pimcore
     */
    public function iLoadPimcore(): void
    {
        $this->pwaPage->open();
        $this->pwaPage->waitTillLoaded();
        $this->pwaPage->verify();
    }
}
