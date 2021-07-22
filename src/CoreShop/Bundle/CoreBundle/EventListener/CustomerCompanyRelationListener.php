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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Pimcore\Event\Model\DataObjectEvent;

final class CustomerCompanyRelationListener
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onCompanyDelete(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if (!$object instanceof CompanyInterface) {
            return;
        }

        $list = $this->customerRepository->getList();
        $list->addConditionParam('company__id = ?', $object->getId());

        /** @var CustomerInterface $customer */
        foreach ($list->getObjects() as $customer) {

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
