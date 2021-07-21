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

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AdminAddressCreationEvent extends Event
{
    private AddressInterface $address;
    private CustomerInterface $customer;
    private array $data;

    public function __construct(AddressInterface $address, CustomerInterface $customer, array $data)
    {
        $this->address = $address;
        $this->customer = $customer;
        $this->data = $data;
    }

    public function getAddress(): AddressInterface
    {
        return $this->address;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
