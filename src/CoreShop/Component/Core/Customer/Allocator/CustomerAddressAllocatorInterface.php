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

namespace CoreShop\Component\Core\Customer\Allocator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

interface CustomerAddressAllocatorInterface
{
    const ADDRESS_ACCESS_TYPE_OWN_ONLY = 'own_only';
    const ADDRESS_ACCESS_TYPE_COMPANY_ONLY = 'company_only';
    const ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY = 'own_and_company';

    const ADDRESS_AFFILIATION_TYPE_OWN = 'own';
    const ADDRESS_AFFILIATION_TYPE_COMPANY = 'company';

    /**
     * @param CustomerInterface $customer
     *
     * @return AddressInterface[]
     * @throws \Exception
     */
    public function allocateForCustomer(CustomerInterface $customer);

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     *
     * @return bool
     */
    public function isOwnerOfAddress(CustomerInterface $customer, AddressInterface $address);
}
