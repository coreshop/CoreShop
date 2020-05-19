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
use CoreShop\Behat\Page\Frontend\Account\ChangePasswordPageInterface;
use CoreShop\Behat\Page\Frontend\Account\ChangeProfilePageInterface;
use CoreShop\Behat\Page\Frontend\Account\ProfilePageInterface;
use CoreShop\Behat\Service\NotificationCheckerInterface;
use CoreShop\Behat\Service\NotificationType;
use CoreShop\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

class CustomerProfileContext implements Context
{
    private $sharedStorage;
    private $changePasswordPage;
    private $profilePage;
    private $changeProfilePage;
    private $notificationChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ChangePasswordPageInterface $changePasswordPage,
        ProfilePageInterface $profilePage,
        ChangeProfilePageInterface $changeProfilePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->changePasswordPage = $changePasswordPage;
        $this->profilePage = $profilePage;
        $this->changeProfilePage = $changeProfilePage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given /^I want to change my password$/
     */
    public function iWantToChangeMyPassword()
    {
        $this->changePasswordPage->open();
    }

    /**
     * @Given I change password from :oldPassword to :newPassword
     */
    public function iChangePasswordTo($oldPassword, $newPassword)
    {
        $this->iSpecifyTheCurrentPasswordAs($oldPassword);
        $this->iSpecifyTheNewPasswordAs($newPassword);
        $this->iSpecifyTheConfirmationPasswordAs($newPassword);
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     */
    public function iShouldBeNotifiedThatMyPasswordHasBeenSuccessfullyChanged()
    {
        $this->notificationChecker->checkNotification('Password has been successfully changed', NotificationType::success());
    }

    /**
     * @Given I specify the current password as :password
     */
    public function iSpecifyTheCurrentPasswordAs($password)
    {
        $this->changePasswordPage->specifyCurrentPassword($password);
    }

    /**
     * @Given I specify the new password as :password
     */
    public function iSpecifyTheNewPasswordAs($password)
    {
        $this->changePasswordPage->specifyNewPassword($password);
    }

    /**
     * @Given I confirm this password as :password
     */
    public function iSpecifyTheConfirmationPasswordAs($password)
    {
        $this->changePasswordPage->specifyConfirmationPassword($password);
    }

    /**
     * @When I save my new password
     */
    public function iSaveMyChanges()
    {
        $this->changePasswordPage->save();
    }


    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch()
    {
        Assert::true($this->changePasswordPage->checkValidationMessageFor(
            'new_password',
            'The password fields must match.'
        ));
    }

    /**
     * @Then I should be notified that provided password is different than the current one
     */
    public function iShouldBeNotifiedThatProvidedPasswordIsDifferentThanTheCurrentOne()
    {
        Assert::true($this->changePasswordPage->checkValidationMessageFor(
            'current_password',
            'This value should be the user\'s current password.'
        ));
    }

    /**
     * @Then my name should be :name
     * @Then my name should still be :name
     */
    public function myNameShouldBe($name)
    {
        $this->profilePage->open();

        Assert::true($this->profilePage->hasCustomerName($name));
    }

    /**
     * @Then my email should be :email
     * @Then my email should still be :email
     */
    public function myEmailShouldBe($email)
    {
        $this->profilePage->open();

        Assert::true($this->profilePage->hasCustomerEmail($email));
    }

    /**
     * @When /^I want to change my personal information$/
     */
    public function iWantToChangeMyPersonalInformation()
    {
        $this->changeProfilePage->open();
    }

    /**
     * @When I specify the new first name as :firstname
     * @When I remove the first name
     */
    public function iSpecifyTheNewFirstnameAs(?string $firstname = null)
    {
        $this->changeProfilePage->specifyFirstname($firstname);
    }

    /**
     * @When I specify the new last name as :lastname
     * @When I remove the last name
     */
    public function iSpecifyTheNewLastnameAs(?string $lastname = null)
    {
        $this->changeProfilePage->specifyLastname($lastname);
    }

    /**
     * @When I specify the new email as :email
     * @When I remove the email
     */
    public function iSpecifiyTheNewEmailAs(?string $email = null)
    {
        $this->changeProfilePage->specifyEmail($email);

        $this->sharedStorage->set('email', $email);
    }

    /**
     * @When /^I confirm (this email)$/
     * @When I confirm email as :email
     * @When /^I also remove the confirm email$/
     */
    public function iConfirmThisEmail(?string $email = null): void
    {
        $this->changeProfilePage->specifyConfirmationEmail($email);
    }

    /**
     * @Given I save my personal information
     */
    public function iSaveMyPersonalInformation()
    {
        $this->changeProfilePage->save();
    }

    /**
     * @Then /^I should be notified that the ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::true($this->changeProfilePage->checkValidationMessageFor(
            $element,
            'This value should not be blank.'
        ));
    }
    /**
     * @Then /^I should be notified that the ([^"]+) is invalid$/
     */
    public function iShouldBeNotifiedThatElementIsInvalid(string $element): void
    {
        Assert::true($this->changeProfilePage->checkValidationMessageFor(
            $element,
            'This value is not a valid email address.'
        ));
    }

    /**
     * @Then /^I should be notified that the ([^"]+) does not match$/
     */
    public function iShouldBeNotifiedThatThePasswordDoNotMatch(string $element): void
    {
        Assert::true($this->changeProfilePage->checkValidationMessageFor(
            $element,
            sprintf('The %s fields must match.', $element)
        ));
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed(): void
    {
        Assert::true($this->changeProfilePage->checkValidationMessageFor(
            'email',
            'This email is already used.'
        ));
    }
}
