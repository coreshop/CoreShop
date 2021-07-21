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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Locale\Model\LocaleAwareInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CustomerInterface extends ResourceInterface, PimcoreModelInterface, LocaleAwareInterface
{
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
