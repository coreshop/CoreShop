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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionStorageManager implements StorageListManagerInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private string $name,
        private FactoryInterface $sessionListFactory,
    ) {
    }

    public function getStorageList(): StorageListInterface
    {
        $list = $this->requestStack->getSession()->get($this->name);

        if (!$list instanceof StorageListInterface) {
            $list = $this->sessionListFactory->createNew();
        }

        return $list;
    }

    public function hasStorageList(): bool
    {
        return $this->requestStack->getSession()->has($this->name);
    }

    public function persist(StorageListInterface $storageList): void
    {
        $this->requestStack->getSession()->set($this->name, $storageList);
    }
}
