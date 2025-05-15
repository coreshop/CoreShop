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

namespace CoreShop\Component\Core\Customer\Address;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

interface AddressAssignmentManagerInterface
{
    public function getAddressAffiliationTypesForCustomer(CustomerInterface $customer, bool $useTranslationKeys = true): ?array;

    public function detectAddressAffiliationForCustomer(CustomerInterface $customer, AddressInterface $address): ?string;

    public function checkAddressAffiliationPermissionForCustomer(CustomerInterface $customer, AddressInterface $address): bool;

    public function allocateAddressByAffiliation(CustomerInterface $customer, AddressInterface $address, ?string $affiliation): AddressInterface;
}
