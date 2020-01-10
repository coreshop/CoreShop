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

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RegistrationService implements RegistrationServiceInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ObjectServiceInterface
     */
    private $objectService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var string
     */
    private $customerFolder;

    /**
     * @var string
     */
    private $guestFolder;

    /**
     * @var string
     */
    private $addressFolder;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param ObjectServiceInterface      $objectService
     * @param EventDispatcherInterface    $eventDispatcher
     * @param LocaleContextInterface      $localeContext
     * @param string                      $customerFolder
     * @param string                      $guestFolder
     * @param string                      $addressFolder
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        ObjectServiceInterface $objectService,
        EventDispatcherInterface $eventDispatcher,
        LocaleContextInterface $localeContext,
        $customerFolder,
        $guestFolder,
        $addressFolder
    ) {
        $this->customerRepository = $customerRepository;
        $this->objectService = $objectService;
        $this->eventDispatcher = $eventDispatcher;
        $this->localeContext = $localeContext;
        $this->customerFolder = $customerFolder;
        $this->guestFolder = $guestFolder;
        $this->addressFolder = $addressFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCustomer(
        CustomerInterface $customer,
        AddressInterface $address,
        $formData,
        $isGuest = false
    ) {
        $existingCustomer = $this->customerRepository->findCustomerByEmail($customer->getEmail());

        if ($existingCustomer instanceof CustomerInterface && !$existingCustomer->getIsGuest()) {
            throw new CustomerAlreadyExistsException();
        }

        $customer->setPublished(true);
        $customer->setParent($this->objectService->createFolderByPath(sprintf(
            '/%s/%s',
            ($isGuest ? $this->guestFolder : $this->customerFolder),
            mb_strtoupper(mb_substr($customer->getLastname(), 0, 1))
        )));
        $customer->setKey(File::getValidFilename($customer->getEmail()));
        $customer->setKey(Service::getUniqueKey($customer));
        $customer->setIsGuest($isGuest);
        $customer->setLocaleCode($this->localeContext->getLocaleCode());
        $customer->save();

        $address->setPublished(true);
        $address->setKey(uniqid());
        $address->setParent($this->objectService->createFolderByPath(sprintf(
            '/%s/%s',
            $customer->getFullPath(),
            $this->addressFolder
        )));
        $address->save();

        $customer->setDefaultAddress($address);
        $customer->addAddress($address);

        $this->eventDispatcher->dispatch(
            'coreshop.customer.register',
            new CustomerRegistrationEvent($customer, $formData)
        );

        $customer->save();
    }
}
