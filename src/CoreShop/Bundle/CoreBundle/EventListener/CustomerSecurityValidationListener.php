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

use CoreShop\Bundle\CoreBundle\Customer\CustomerLoginServiceInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Http\RequestHelper;
use Pimcore\Model\Element\ValidationException;

final class CustomerSecurityValidationListener
{
    /**
     * @var RequestHelper
     */
    protected $requestHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $loginIdentifier;

    /**
     * @param RequestHelper               $requestHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param string                      $className
     * @param string                      $loginIdentifier
     */
    public function __construct(
        RequestHelper $requestHelper,
        CustomerRepositoryInterface $customerRepository,
        $className,
        $loginIdentifier
    ) {
        $this->requestHelper = $requestHelper;
        $this->customerRepository = $customerRepository;
        $this->className = $className;
        $this->loginIdentifier = $loginIdentifier;
    }

    /**
     * @param DataObjectEvent $event
     *
     * @throws ValidationException
     */
    public function checkCustomerSecurityDataBeforeUpdate(DataObjectEvent $event)
    {
        if ($this->requestHelper->hasCurrentRequest() && !$this->requestHelper->isFrontendRequestByAdmin()) {
            return;
        }

        $object = $event->getObject();

        if (!$object instanceof CustomerInterface) {
            return;
        }

        if ($object->getIsGuest() === true) {
            return;
        }

        $identifierValue = $this->loginIdentifier === 'email' ? $object->getEmail() : $object->getUsername();

        $listing = $this->customerRepository->getList();
        $listing->setUnpublished(true);
        $listing->addConditionParam(sprintf('%s = ?', $this->loginIdentifier), $identifierValue);
        $listing->addConditionParam('o_id != ?', $object->getId());

        $objects = $listing->getObjects();

        if (count($objects) === 0) {
            return;
        }

        throw new ValidationException(sprintf('%s "%s" is already used. Please use another one.', ucfirst($this->loginIdentifier), $identifierValue));
    }
}
