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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\OrderItem as BaseOrderItem;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

abstract class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    public function getWeight()
    {
        return $this->getTotalWeight();
    }

    public function getWidth()
    {
        $product = $this->getProduct();

        return $product instanceof ProductInterface ? $product->getWidth() : 0;
    }

    public function getHeight()
    {
        $product = $this->getProduct();

        return $product instanceof ProductInterface ? $product->getHeight() : 0;
    }

    public function getDepth()
    {
        $product = $this->getProduct();

        return $product instanceof ProductInterface ? $product->getDepth() : 0;
    }

    public function hasUnitDefinition(): bool
    {
        return $this->getUnitDefinition() instanceof ProductUnitDefinitionInterface;
    }

    public function equals(StorageListItemInterface $storageListItem): bool
    {
        $coreEquals = parent::equals($storageListItem);

        if ($coreEquals === false) {
            return false;
        }

        if (!$this->hasUnitDefinition()) {
            return $coreEquals;
        }

        if (!$storageListItem instanceof OrderItemInterface) {
            return $coreEquals;
        }

        if (!$storageListItem->hasUnitDefinition()) {
            return $coreEquals;
        }

        return $storageListItem->getUnitDefinition()->getId() === $this->getUnitDefinition()->getId();
    }
}
