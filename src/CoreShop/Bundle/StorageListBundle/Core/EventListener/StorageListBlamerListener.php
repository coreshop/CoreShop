<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\StorageListBundle\Core\EventListener;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Customer\Model\CustomerAwareInterface;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListNotFoundException;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class StorageListBlamerListener
{
    public function __construct(private StorageListContextInterface $context)
    {
    }

    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        $customer = $user->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->blame($customer);
    }

    public function onRegisterEvent(CustomerRegistrationEvent $event): void
    {
        $user = $event->getCustomer();

        $this->blame($user);
    }

    private function blame(CustomerInterface $user): void
    {
        $storageList = $this->getStorageList();

        if (null === $storageList) {
            return;
        }
        
        if (!$storageList instanceof CustomerAwareInterface) {
            return;
        }

        $storageList->setCustomer($user);
    }

    private function getStorageList(): ?StorageListInterface
    {
        try {
            return $this->context->getStorageList();
        } catch (StorageListNotFoundException) {
            return null;
        }

        return null;
    }
}
