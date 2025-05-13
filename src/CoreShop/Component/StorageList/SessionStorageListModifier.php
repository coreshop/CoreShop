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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
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
