<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
