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

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Core\Repository\CustomerAndStoreAwareRepositoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Resolver\StorageListResolverInterface;

class PimcoreListResolver implements StorageListResolverInterface
{
    public function __construct(
        protected CustomerAndStoreAwareRepositoryInterface $repository,
        protected ShopperContextInterface $context,
        protected StorageListContextInterface $storageListContext,
    )
    {
    }

    public function getStorageLists(): array
    {
        if (!$this->context->hasCustomer()) {
            return [$this->storageListContext->getStorageList()];
        }

        $namedLists = $this->repository->findNamedStorageLists($this->context->getStore(), $this->context->getCustomer());

        $defaultList = $this->storageListContext->getStorageList();
        array_unshift($namedLists, $defaultList);

        return $namedLists;
    }

    public function findNamed(string $name): ?StorageListInterface
    {
        if (!$this->context->hasCustomer()) {
            return null;
        }

        return $this->repository->findLatestByStoreAndCustomer($this->context->getStore(), $this->context->getCustomer(), $name);
    }
}