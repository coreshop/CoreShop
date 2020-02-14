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

namespace CoreShop\Component\Core\Customer\Address;

use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Customer\Model\CompanyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

final class AddressAccessOptionsProvider implements SelectOptionsProviderInterface
{
    /**
     * @param array $context
     * @param Data  $fieldDefinition
     *
     * @return array
     */
    public function getOptions($context, $fieldDefinition)
    {
        $object = $context['object'];

        if (!$object instanceof CustomerInterface) {
            return [];
        }

        $types = [
            ['value' => CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY, 'key' => 'Own Only']
        ];

        if ($object->getCompany() instanceof CompanyInterface) {
            $types[] = ['value' => CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_COMPANY_ONLY, 'key' => 'Company Only'];
            $types[] = ['value' => CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY, 'key' => 'Own & Company'];
        }

        return $types;
    }

    /**
     * @param $context         array
     * @param $fieldDefinition Data
     *
     * @return mixed
     */
    public function getDefaultValue($context, $fieldDefinition)
    {
        return CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY;
    }

    /**
     * @param $context         array
     * @param $fieldDefinition Data
     *
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition)
    {
        return false;
    }

}
