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

namespace CoreShop\Behat\Element\Frontend\Account;

use Behat\Mink\Exception\ElementNotFoundException;

interface RegisterElementInterface
{
    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function register(): void;

    public function getEmail(): string;

    public function specifySalutation(?string $salutation): void;

    public function specifyGender(?string $gender): void;

    public function specifyFirstname(?string $firstname): void;

    public function specifyLastname(?string $lastname): void;

    public function specifyEmail(?string $email): void;

    public function verifyEmail(?string $email): void;

    public function specifyPassword(?string $password): void;

    public function verifyPassword(?string $password): void;

    public function specifyAddressCompany(?string $company): void;

    public function specifyAddressSalutation(?string $salutation): void;

    public function specifyAddressFirstname(?string $firstName): void;

    public function specifyAddressLastname(?string $lastname): void;

    public function specifyAddressStreet(?string $street): void;

    public function specifyAddressNumber(?string $number): void;

    public function specifyAddressPostcode(?string $postcode): void;

    public function specifyAddressCity(?string $city): void;

    public function specifyAddressCountry(?int $country): void;

    public function specifyAddressPhoneNumber(?string $phoneNumber): void;

    public function acceptTermsOfService(): void;

    public function subscribeToTheNewsletter(): void;
}
