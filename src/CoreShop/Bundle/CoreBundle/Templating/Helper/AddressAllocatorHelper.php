<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Templating\Helper\Helper;

class AddressAllocatorHelper extends Helper implements AddressAllocatorHelperInterface
{
    /**
     * @var CustomerAddressAllocatorInterface
     */
    private $customerAddressAllocator;

    /**
     * @param CustomerAddressAllocatorInterface $customerAddressAllocator
     */
    public function __construct(CustomerAddressAllocatorInterface $customerAddressAllocator)
    {
        $this->customerAddressAllocator = $customerAddressAllocator;
    }

    /**
     * {@inheritdoc}
     */
    public function allocateAddresses(CustomerInterface $customer)
    {
        return $this->customerAddressAllocator->allocateForCustomer($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function isOwnerOfAddress(CustomerInterface $customer, AddressInterface $address)
    {
        return $this->customerAddressAllocator->isOwnerOfAddress($customer, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_address_allocator';
    }
}
