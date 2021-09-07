<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getWidth() : 0;
    }

    public function getHeight()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getHeight() : 0;
    }

    public function getDepth()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getDepth() : 0;
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
