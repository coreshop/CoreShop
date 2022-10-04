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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Pimcore\Event\Model\DataObjectEvent;

final class CustomerCompanyRelationListener
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function onCompanyDelete(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof CompanyInterface) {
            return;
        }

        $list = $this->customerRepository->getList();
        $list->addConditionParam('company__id = ?', $object->getId());

        /** @var CustomerInterface $customer */
        foreach ($list->getData() as $customer) {
            $accessType = $customer->getAddressAccessType();
            if (empty($accessType)) {
                continue;
            }
            if ($accessType === CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY) {
                continue;
            }

            $customer->setAddressAccessType(CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY);
            $customer->save();
        }
    }
}
