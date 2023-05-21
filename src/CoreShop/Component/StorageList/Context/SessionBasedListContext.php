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

namespace CoreShop\Component\StorageList\Context;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Repository\StorageListRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class SessionBasedListContext implements StorageListContextInterface
{
    private ?StorageListInterface $storageList = null;

    public function __construct(
        private StorageListContextInterface $inner,
        private RequestStack $requestStack,
        private StorageListRepositoryInterface $repository,
        private string $sessionKeyName,
    ) {
    }

    public function getStorageList(): StorageListInterface
    {
        if (null !== $this->storageList) {
            return $this->storageList;
        }

        $request = $this->requestStack->getMainRequest();

        if (!$request) {
            return $this->inner->getStorageList();
        }

        $session = $request->getSession();

        $sessionId = $session->get($this->sessionKeyName);

        if (null === $sessionId) {
            $storageList = $this->inner->getStorageList();
        } else {
            $storageList = $this->repository->find($sessionId);
        }

        if (!$storageList instanceof StorageListInterface) {
            $this->storageList = $this->inner->getStorageList();
        } else {
            $this->storageList = $storageList;
        }

        return $this->storageList;
    }
}
