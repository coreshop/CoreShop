<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Address\Model;

trait AddressesAwareTrait
{
    /**
     * @var AddressInterface[]
     */
    protected $addresses;

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }
}
