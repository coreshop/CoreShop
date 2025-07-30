<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class SessionStorageListModifier extends SimpleStorageListModifier
{
    public function __construct(
        private StorageListManagerInterface $manager,
    ) {
        parent::__construct();
    }

    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        parent::addToList($storageList, $item);

        $this->manager->persist($storageList);
    }

    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        parent::removeFromList($storageList, $item);

        $this->manager->persist($storageList);
    }
}
