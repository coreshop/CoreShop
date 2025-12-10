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

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerRegistrationEvent extends GenericEvent
{
    private CustomerInterface $customer;

    private array $data;

    public function __construct(
        CustomerInterface $customer,
        array $data,
    ) {
        parent::__construct($customer);

        $this->customer = $customer;
        $this->data = $data;
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
