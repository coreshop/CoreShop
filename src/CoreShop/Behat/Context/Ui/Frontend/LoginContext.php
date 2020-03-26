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
use CoreShop\Behat\Page\Frontend\Account\LoginPageInterface;
use CoreShop\Behat\Page\Frontend\HomePageInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    private $homePage;
    private $loginPage;

    public function __construct(
        HomePageInterface $homePage,
        LoginPageInterface $loginPage
    ) {
        $this->homePage = $homePage;
        $this->loginPage = $loginPage;
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->loginPage->open();
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername(?string $username = null): void
    {
        $this->loginPage->specifyUsername($username);
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs(?string $password = null): void
    {
        $this->loginPage->specifyPassword($password);
    }

    /**
     * @When I log in
     * @When I try to log in
     */
    public function iLogIn(): void
    {
        $this->loginPage->logIn();
    }

    /**
     * @When I sign in with email :email and password :password
     */
    public function iSignInWithEmailAndPassword(string $email, string $password): void
    {
        $this->iWantToLogIn();
        $this->iSpecifyTheUsername($email);
        $this->iSpecifyThePasswordAs($password);
        $this->iLogIn();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        $this->homePage->verify();
        Assert::true($this->homePage->hasLogoutButton());
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        Assert::false($this->homePage->hasLogoutButton());
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials(): void
    {
        Assert::true($this->loginPage->hasValidationErrorWith('Invalid credentials.'));
    }

    /**
     * @Then I should be able to log in as :email with :password password
     * @Then the customer should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();

        $this->iShouldBeLoggedIn();
    }
}
