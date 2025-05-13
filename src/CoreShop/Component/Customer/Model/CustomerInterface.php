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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Locale\Model\LocaleAwareInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CustomerInterface extends ResourceInterface, PimcoreModelInterface, LocaleAwareInterface
{
    public function getId(): ?int;

    public function getSalutation(): ?string;

    public function setSalutation(?string $salutation);

    public function getFirstname(): ?string;

    public function setFirstname(?string $firstname);

    public function getLastname(): ?string;

    public function setLastname(?string $lastname);

    public function getGender(): ?string;

    public function setGender(?string $gender);

    public function getEmail(): ?string;

    public function setEmail(?string $email);

    public function getCustomerGroups(): ?array;

    public function setCustomerGroups(?array $customerGroups);

    public function getCompany(): ?CompanyInterface;

    public function setCompany(?CompanyInterface $company);
}
