<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Page\Frontend\Checkout;

use CoreShop\Behat\Page\Frontend\FrontendPageInterface;

interface CustomerPageInterface extends FrontendPageInterface
{
    public function specifyGuestGender(?string $gender): void;

    public function specifyGuestFirstname(?string $firstname): void;

    public function specifyGuestLastname(?string $lastname): void;

    public function specifyGuestEmail(?string $email): void;

    public function specifyGuestEmailRepeat(?string $email): void;

    public function specifyGuestAddressCompany(?string $company): void;

    public function specifyGuestAddressSalutation(?string $salutation): void;

    public function specifyGuestAddressFirstname(?string $firstname): void;

    public function specifyGuestAddressLastname(?string $lastname): void;

    public function specifyGuestAddressStreet(?string $street): void;

    public function specifyGuestAddressNumber(?string $number): void;

    public function specifyGuestAddressPostcode(?string $postcode): void;

    public function specifyGuestAddressCity(?string $city): void;

    public function specifyGuestAddressPhoneNumber(?string $number): void;

    public function specifyGuestAddressCountry(?int $country): void;

    public function acceptTermsOfService(): void;

    public function submitGuestCheckout(): void;
}
