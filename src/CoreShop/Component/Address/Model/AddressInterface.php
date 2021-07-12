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

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface AddressInterface extends ResourceInterface, PimcoreModelInterface
{
    public function getSalutation(): ?string;

    public function setSalutation(?string $salutation);

    public function getFirstname(): ?string;

    public function setFirstname(?string $firstname);

    public function getLastname(): ?string;

    public function setLastname(?string $lastname);

    public function getCompany(): ?string;

    public function setCompany(?string $company);

    public function getStreet(): ?string;

    public function setStreet(?string $street);

    public function getNumber(): ?string;

    public function setNumber(?string $number);

    public function getPostcode(): ?string;

    public function setPostcode(?string $postcode);

    public function getCity(): ?string;

    public function setCity(?string $city);

    public function getCountry(): ?CountryInterface;

    public function setCountry(?CountryInterface $country);

    public function getState(): ?StateInterface;

    public function setState(?StateInterface $state);

    public function getPhoneNumber();

    public function setPhoneNumber(?string $phoneNumber);

    public function getAddressIdentifier(): ?AddressIdentifierInterface;

    public function setAddressIdentifier(?AddressIdentifierInterface $addressIdentifier);

    public function hasAddressIdentifier(): bool;
}
