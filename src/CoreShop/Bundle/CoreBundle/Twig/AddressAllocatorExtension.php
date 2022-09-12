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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

final class AddressAllocatorExtension extends AbstractExtension
{
    public function __construct(private CustomerAddressAllocatorInterface $customerAddressAllocator)
    {
    }

    public function getTests(): array
    {
        return [
            new TwigTest('coreshop_address_owner_of', [$this->customerAddressAllocator, 'isOwnerOfAddress']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_allocate_valid_addresses', [$this->customerAddressAllocator, 'allocateForCustomer']),
        ];
    }
}
