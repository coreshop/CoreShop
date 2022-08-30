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

namespace CoreShop\Component\StorageList\Core\Context;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListNotFoundException;
use CoreShop\Component\StorageList\Model\StorageListInterface;
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
        private RepositoryInterface $repository,
        private StoreContextInterface $storeContext
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
        $storageList = $this->repository->find($storageListId);

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
