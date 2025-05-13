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
use CoreShop\Behat\Page\Pimcore\LoginPageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    public function __construct(
        private LoginPageInterface $adminLoginPage,
        private PWAPageInterface $pwaPage,
    ) {
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
