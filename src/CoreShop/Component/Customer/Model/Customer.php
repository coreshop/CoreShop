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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class Customer extends AbstractPimcoreModel implements CustomerInterface
{
    public function getSalutation(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setSalutation(?string $salutation)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getFirstname(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setFirstname(?string $firstname)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getLastname(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setLastname(?string $lastname)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getGender(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setGender(?string $gender)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getEmail(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setEmail(?string $email)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getLocaleCode(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setLocaleCode(?string $locale)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getCustomerGroups(): ?array
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setCustomerGroups(?array $customerGroups)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
