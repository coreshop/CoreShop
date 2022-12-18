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

namespace CoreShop\Component\StorageList\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

/**
 * @method setProduct($product)
 */
interface StorageListItemInterface
{
    public function getId();

    public function equals(self $storageListItem): bool;

    public function getProduct(): ?ResourceInterface;

//    public function setProduct($product);

    public function getQuantity(): ?float;

    public function setQuantity(?float $quantity);

    public function getStorageList(): ?StorageListInterface;

    public function setStorageList(StorageListInterface $storageList);
}
