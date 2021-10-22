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

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class CustomerManager implements CustomerManagerInterface
{
    public function __construct(private EventDispatcherInterface $eventDispatcher, private FolderCreationServiceInterface $folderCreationService, private string $loginIdentifier)
    {
    }

    public function persistCustomer(CustomerInterface $customer): void
    {
        /**
         * @var Concrete $customer
         */
        Assert::isInstanceOf($customer, Concrete::class);

        /**
         * @var AddressInterface[] $addressBackup
         */
        $addressBackup = $customer->getObjectVar('addresses');

        /**
         * @var UserInterface|null $userBackup
         */
        $userBackup = $customer->getObjectVar('user');

        /**
         * @var CustomerInterface $customer
         */
        $customer->setUser(null);
        $customer->setAddresses([]);
        $customer->setPublished(true);
        $customer->setParent(
            $this->folderCreationService->createFolderForResource($customer, [
                'path' => ($userBackup ? 'customer' : 'guest'),
                'suffix' => mb_strtoupper(mb_substr($customer->getLastname(), 0, 1)),
            ])
        );
        /** @psalm-suppress InternalMethod */
        $customer->setKey(File::getValidFilename($customer->getEmail()));
        /** @psalm-suppress InvalidArgument */
        $customer->setKey(Service::getUniqueKey($customer));
        $customer->save();

        foreach ($addressBackup as $address) {
            $address->setPublished(true);
            $address->setKey(uniqid());
            $address->setParent(
                $this->folderCreationService->createFolderForResource($address, [
                    'prefix' => $customer->getFullPath(),
                ])
            );
            $address->save();
        }

        if ($userBackup) {
            if ($this->loginIdentifier === 'email') {
                $userBackup->setLoginIdentifier($customer->getEmail());
            }
            $userBackup->setCustomer($customer);
            $userBackup->setPassword($userBackup->getPlainPassword());
            $userBackup->setPublished(true);
            $userBackup->setParent(
                $this->folderCreationService->createFolderForResource($userBackup, [
                    'prefix' => $customer->getFullPath(),
                ])
            );
            /** @psalm-suppress InternalMethod */
            $userBackup->setKey(File::getValidFilename($customer->getEmail()));
            /** @psalm-suppress InvalidArgument */
            $userBackup->setKey(Service::getUniqueKey($userBackup));
            $userBackup->save();
        }

        if (count($addressBackup) > 0) {
            $customer->setDefaultAddress($addressBackup[0]);
        }

        $customer->setUser($userBackup);
        $customer->setAddresses($addressBackup);
        $customer->save();

        $this->eventDispatcher->dispatch(new CustomerRegistrationEvent($customer, []), 'coreshop.customer.register');
    }
}
