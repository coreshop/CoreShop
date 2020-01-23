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

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\Event;

final class AdminAddressCreationEvent extends Event
{
    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @var array
     */
    private $data;

    public function __construct(AddressInterface $address, CustomerInterface $customer, array $data)
    {
        $this->address = $address;
        $this->customer = $customer;
        $this->data = $data;
    }

    /**
     * @return AddressInterface
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
