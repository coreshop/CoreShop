<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Page\Frontend\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use CoreShop\Bundle\TestBundle\Page\Frontend\AbstractFrontendPage;
use CoreShop\Bundle\TestBundle\Service\DriverHelper;
use CoreShop\Component\Address\Model\CountryInterface;

class ChangeAddressPage extends AbstractFrontendPage implements ChangeAddressPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_customer_addresses';
    }

    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement('address-validation-errors')->getValue();

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $errorLabel;
    }

    public function specifyFirstname(?string $firstname = null): void
    {
        $this->getElement('address-firstname')->setValue($firstname);
    }

    public function specifyLastname(?string $lastname = null): void
    {
        $this->getElement('address-lastname')->setValue($lastname);
    }

    public function specifyStreet(?string $street = null, ?string $number = null): void
    {
        $this->getElement('address-street')->setValue($street);
        $this->getElement('address-number')->setValue($number);
    }

    public function specifyPhoneNumber(string $phoneNumber): void
    {
        $this->getElement('address-phoneNumber')->setValue($phoneNumber);
    }

    public function openLink(?string $street): void
    {
        $this->getElement('addresses-link', ['%street%' => $street])->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function addAddress(): void
    {
        $this->getElement('address-add')->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function deleteAddress($street): void
    {
        $this->getElement('address-delete', ['%street%' => $street])->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function fillAddress(
        ?CountryInterface $country = null,
        ?string $city = null,
        ?string $postcode = null,
        ?string $number = null,
        ?string $street = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $salutation = null,
        ?string $phone = null,
    ): void {
        $this->getElement('address-phoneNumber')->setValue($phone);
        $this->getElement('address-country')->setValue($country?->getId());
        $this->getElement('address-city')->setValue($city);
        $this->getElement('address-postcode')->setValue($postcode);
        $this->getElement('address-number')->setValue($number);
        $this->getElement('address-street')->setValue($street);
        $this->getElement('address-lastname')->setValue($lastname);
        $this->getElement('address-firstname')->setValue($firstname);
    }

    public function save(): void
    {
        $this->getElement('save_changes')->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'save_changes' => '[data-test-address-save-changes]',
            'address-phoneNumber' => '[data-test-address-phoneNumber]',
            'address-country' => '[data-test-address-country]',
            'address-city' => '[data-test-address-city]',
            'address-postcode' => '[data-test-address-postcode]',
            'address-number' => '[data-test-address-number]',
            'address-street' => '[data-test-address-street]',
            'address-lastname' => '[data-test-address-lastname]',
            'address-firstname' => '[data-test-address-firstname]',
            'address-salutation' => '[data-test-address-salutation]',
            'address-validation-errors' => '[data-test-address-validation-errors]',
            'addresses-link' => '[data-test-addresses-link="%street%"]',
            'address-add' => '[data-test-address-add]',
            'address-delete' => '[data-test-address-delete="%street%"]',
        ]);
    }
}
