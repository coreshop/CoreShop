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

namespace CoreShop\Component\StorageList\Core\Context;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListNotFoundException;
use CoreShop\Component\StorageList\Core\Repository\CustomerAndStoreAwareRepositoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class CustomerAndStoreBasedStorageListContext implements StorageListContextInterface
{
    public function __construct(
        private CustomerContextInterface $customerContext,
        private StoreContextInterface $storeContext,
        private CustomerAndStoreAwareRepositoryInterface $repository
    ) {
    }

    public function getStorageList(): StorageListInterface
    {
        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException) {
            throw new StorageListNotFoundException('CoreShop was not able to find the requested list, as there is no current store.');
        }

        try {
            $customer = $this->customerContext->getCustomer();
        } catch (CustomerNotFoundException) {
            throw new StorageListNotFoundException('CoreShop was not able to find the requested list, as there is no logged in user.');
        }

        $storageList = $this->repository->findLatestByStoreAndCustomer($store, $customer);

        if (null === $storageList) {
            throw new StorageListNotFoundException(
                'CoreShop was not able to find the requested list for currently logged in user.'
            );
        }

        return $storageList;
    }
}
