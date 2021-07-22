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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;

interface AddressAllocatorHelperInterface
{
    /**
     * @param CustomerInterface $address
     *
     * @return AddressInterface[]
     */
    public function allocateAddresses(CustomerInterface $address);

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     *
     * @return bool
     */
    public function isOwnerOfAddress(CustomerInterface $customer, AddressInterface $address);
}
