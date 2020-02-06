<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class CustomerManager implements CustomerManagerInterface
{
    /**
     * @var ObjectServiceInterface
     */
    private $objectService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

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
    private $userFolder;

    /**
     * @var string
     */
    private $addressFolder;

    /**
     * @param ObjectServiceInterface   $objectService
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $customerFolder
     * @param string                   $guestFolder
     * @param string                   $userFolder
     * @param string                   $addressFolder
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        EventDispatcherInterface $eventDispatcher,
        string $customerFolder,
        string $guestFolder,
        string $userFolder,
        string $addressFolder
    ) {
        $this->objectService = $objectService;
        $this->eventDispatcher = $eventDispatcher;
        $this->customerFolder = $customerFolder;
        $this->guestFolder = $guestFolder;
        $this->userFolder = $userFolder;
        $this->addressFolder = $addressFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function persistCustomer(CustomerInterface $customer)
    {
        /**
         * @var Concrete $customer
         */
        Assert::isInstanceOf($customer, Concrete::class);

        $addressBackup = $customer->getObjectVar('addresses');
        $userBackup = $customer->getObjectVar('user');

        $customer->setUser(null);
        $customer->setAddresses([]);
        $customer->setPublished(true);
        $customer->setParent($this->objectService->createFolderByPath(sprintf('/%s/%s', ($userBackup ? $this->customerFolder : $this->guestFolder), mb_strtoupper(mb_substr($customer->getLastname(), 0, 1)))));
        $customer->setKey(File::getValidFilename($customer->getEmail()));
        $customer->setKey(Service::getUniqueKey($customer));
        $customer->save();

        foreach ($addressBackup as $address) {
            $address->setPublished(true);
            $address->setKey(uniqid());
            $address->setParent($this->objectService->createFolderByPath(sprintf(
                '/%s/%s',
                $customer->getFullPath(),
                $this->addressFolder
            )));
            $address->save();
        }

        if ($userBackup) {
            $userBackup->setEmail($customer->getEmail());
            $userBackup->setCustomer($customer);
            $userBackup->setPassword($userBackup->getPlainPassword());
            $userBackup->setPublished(true);
            $userBackup->setParent($this->objectService->createFolderByPath(sprintf(
                '/%s/%s',
                $customer->getFullPath(),
                $this->userFolder
            )));
            $userBackup->setKey(File::getValidFilename($customer->getEmail()));
            $userBackup->setKey(Service::getUniqueKey($userBackup));
            $userBackup->save();
        }

        $this->eventDispatcher->dispatch('coreshop.customer.register', new CustomerRegistrationEvent($customer, []));

        if (count($addressBackup) > 0) {
            $customer->setDefaultAddress($addressBackup[0]);
        }

        $customer->setUser($userBackup);
        $customer->setAddresses($addressBackup);
        $customer->save();
    }
}
