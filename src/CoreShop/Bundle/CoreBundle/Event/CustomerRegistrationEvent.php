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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
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
