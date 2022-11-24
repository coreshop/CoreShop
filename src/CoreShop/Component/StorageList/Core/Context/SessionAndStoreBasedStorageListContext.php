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

namespace CoreShop\Component\StorageList\Core\Context;

use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListNotFoundException;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Repository\StorageListRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class SessionAndStoreBasedStorageListContext implements StorageListContextInterface
{
    private ?StorageListInterface $storageList = null;

    public function __construct(
        private RequestStack $requestStack,
        private string $sessionKeyName,
        private StorageListRepositoryInterface $repository,
        private StoreContextInterface $storeContext,
    ) {
    }

    public function getStorageList(): StorageListInterface
    {
        if (null !== $this->storageList) {
            return $this->storageList;
        }

        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException $exception) {
            throw new StorageListNotFoundException($exception->getMessage(), $exception);
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request || !$request->hasSession()) {
            throw new StorageListNotFoundException('CoreShop was not able to find the List in session');
        }

        $session = $request->getSession();

        if (!$session->has(sprintf('%s.%s', $this->sessionKeyName, $store->getId()))) {
            throw new StorageListNotFoundException('CoreShop was not able to find the List in session');
        }

        $storageListId = $session->get(sprintf('%s.%s', $this->sessionKeyName, $store->getId()));

        if (!is_int($storageListId)) {
            throw new StorageListNotFoundException('CoreShop was not able to find the List in session');
        }

        /**
         * @var StorageListInterface|null $storageList
         */
        $storageList = $this->repository->findByStorageListId($storageListId);

        if (!$storageList instanceof StoreAwareInterface) {
            throw new StorageListNotFoundException();
        }

        if (null === $storageList->getStore() || $storageList->getStore()->getId() !== $store->getId()) {
            $storageList = null;
        }

        if (null === $storageList) {
            $session->remove(sprintf('%s.%s', $this->sessionKeyName, $store->getId()));

            throw new StorageListNotFoundException('CoreShop was not able to find the List in session');
        }

        $this->storageList = $storageList;

        return $storageList;
    }
}
