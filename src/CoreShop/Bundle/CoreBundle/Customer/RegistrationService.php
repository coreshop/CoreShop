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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Pimcore\File;
use Pimcore\Model\DataObject\Service;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RegistrationService implements RegistrationServiceInterface
{
    private $customerRepository;
    private $objectService;
    private $eventDispatcher;
    private $localeContext;
    private $customerFolder;
    private $guestFolder;
    private $addressFolder;
    private $loginIdentifier;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        ObjectServiceInterface $objectService,
        EventDispatcherInterface $eventDispatcher,
        LocaleContextInterface $localeContext,
        string $customerFolder,
        string $guestFolder,
        string $addressFolder,
        string $loginIdentifier
    ) {
        $this->customerRepository = $customerRepository;
        $this->objectService = $objectService;
        $this->eventDispatcher = $eventDispatcher;
        $this->localeContext = $localeContext;
        $this->customerFolder = $customerFolder;
        $this->guestFolder = $guestFolder;
        $this->addressFolder = $addressFolder;
        $this->loginIdentifier = $loginIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCustomer(
        CustomerInterface $customer,
        AddressInterface $address,
        array $formData,
        bool $isGuest = false
    ): void {
        $loginIdentifierValue = $this->loginIdentifier === 'email' ? $customer->getEmail() : $customer->getUsername();
        $existingCustomer = $this->customerRepository->findUniqueByLoginIdentifier($this->loginIdentifier, $loginIdentifierValue, $isGuest);

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

        // save customer without version: the real one comes with the next save!
        VersionHelper::useVersioning(function () use ($customer) {
            $customer->save();
        }, false);

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
            new CustomerRegistrationEvent($customer, $formData),
            'coreshop.customer.register'
        );

        $customer->save();
    }
}
