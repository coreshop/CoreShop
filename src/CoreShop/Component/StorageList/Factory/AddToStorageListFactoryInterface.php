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

namespace CoreShop\Component\StorageList\Factory;

use CoreShop\Component\StorageList\DTO\AddToStorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

interface AddToStorageListFactoryInterface
{
    public function createWithStorageListAndStorageListItem(
        StorageListInterface $storageList,
        StorageListItemInterface $storageListItem,
    ): AddToStorageListInterface;
}
