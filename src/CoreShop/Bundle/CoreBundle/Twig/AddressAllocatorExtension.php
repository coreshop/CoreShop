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

declare(strict_types=1);

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
