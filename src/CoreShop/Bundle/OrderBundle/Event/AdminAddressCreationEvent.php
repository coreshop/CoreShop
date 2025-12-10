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

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AdminAddressCreationEvent extends Event
{
    private array $data;

    public function __construct(
        private AddressInterface $address,
        private CustomerInterface $customer,
        array $data,
    ) {
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
