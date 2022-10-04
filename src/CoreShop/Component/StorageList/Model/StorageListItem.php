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

abstract class StorageListItem implements StorageListItemInterface
{
    protected float $quantity = 0;

    protected mixed $product;

    public function equals(StorageListItemInterface $storageListItem): bool
    {
        $product = $storageListItem->getProduct();

        return $this->getProduct() instanceof $product && $this->getProduct()->getId() === $product->getId();
    }

    public function getId()
    {
        return $this->product ? $this->product->getId() : 0;
    }

    public function getProduct(): ResourceInterface
    {
        return $this->product;
    }

    public function setProduct(ResourceInterface $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    /**
     * @return void
     */
    public function setQuantity(?float $quantity)
    {
        $this->quantity = $quantity;
    }
}
