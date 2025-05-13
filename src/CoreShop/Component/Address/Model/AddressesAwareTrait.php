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

namespace CoreShop\Component\Address\Model;

trait AddressesAwareTrait
{
    /**
     * @var AddressInterface[]
     */
    protected $addresses;

    public function hasAddress(AddressInterface $address)
    {
        $addresses = $this->getAddresses();

        if (!is_array($addresses)) {
            return false;
        }

        foreach ($addresses as $existingAddress) {
            if ($existingAddress instanceof AddressInterface && $existingAddress->getId() === $address->getId()) {
                return true;
            }
        }

        return false;
    }

    public function addAddress(AddressInterface $address)
    {
        $addresses = $this->getAddresses();

        if (!is_array($addresses)) {
            $addresses = [];
        }

        if (!$this->hasAddress($address)) {
            $addresses[] = $address;
            $this->setAddresses($addresses);
        }
    }

    public function removeAddress(AddressInterface $address)
    {
        if (!$this->hasAddress($address)) {
            return;
        }

        $this->setAddresses(array_filter($this->getAddresses(), function (AddressInterface $storedAddress) use ($address) {
            return $storedAddress->getId() !== $address->getId();
        }));
    }

    public function getAddresses(): ?array
    {
        return $this->addresses;
    }

    public function setAddresses(?array $addresses)
    {
        $this->addresses = $addresses;
    }
}
