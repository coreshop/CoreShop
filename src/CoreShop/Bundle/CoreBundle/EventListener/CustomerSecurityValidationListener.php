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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Http\RequestHelper;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\Element\ValidationException;

final class CustomerSecurityValidationListener
{
    public function __construct(protected RequestHelper $requestHelper, protected CustomerRepositoryInterface $customerRepository, protected string $className)
    {
    }

    public function checkCustomerSecurityDataBeforeUpdate(DataObjectEvent $event): void
    {
        if ($this->requestHelper->hasCurrentRequest() && !$this->requestHelper->isFrontendRequestByAdmin()) {
            return;
        }

        $object = $event->getObject();

        if (!$object instanceof CustomerInterface) {
            return;
        }

        if (null === $object->getUser()) {
            return;
        }

        $identifierValue = $object->getEmail();

        /**
         * @var Listing $listing
         */
        $listing = $this->customerRepository->getList();
        $listing->setUnpublished(true);
        $listing->addConditionParam('email', $identifierValue);
        $listing->addConditionParam('o_id != ?', $object->getId());
        $listing->addConditionParam('user__id IS NOT NULL');

        $objects = $listing->getObjects();

        if (0 === count($objects)) {
            return;
        }

        throw new ValidationException(sprintf('Email "%s" is already used. Please use another one.', $identifierValue));
    }
}
