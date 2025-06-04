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

namespace CoreShop\Component\Customer\Context;

use CoreShop\Component\Customer\Model\CustomerInterface;

final class FixedCustomerContext implements CustomerContextInterface
{
    private ?CustomerInterface $customer = null;

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getCustomer(): CustomerInterface
    {
        if ($this->customer instanceof CustomerInterface) {
            return $this->customer;
        }

        throw new CustomerNotFoundException();
    }
}
