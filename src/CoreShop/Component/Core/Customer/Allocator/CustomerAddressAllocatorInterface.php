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

namespace CoreShop\Component\Core\Customer\Allocator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

interface CustomerAddressAllocatorInterface
{
    public const ADDRESS_ACCESS_TYPE_OWN_ONLY = 'own_only';

    public const ADDRESS_ACCESS_TYPE_COMPANY_ONLY = 'company_only';

    public const ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY = 'own_and_company';

    public const ADDRESS_AFFILIATION_TYPE_OWN = 'own';

    public const ADDRESS_AFFILIATION_TYPE_COMPANY = 'company';

    public function allocateForCustomer(CustomerInterface $customer): array;

    public function isOwnerOfAddress(CustomerInterface $customer, AddressInterface $address): bool;
}
