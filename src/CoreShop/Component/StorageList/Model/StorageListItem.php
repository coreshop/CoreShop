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

namespace CoreShop\Component\StorageList\Model;

class StorageListItem implements StorageListItemInterface
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

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity)
    {
        $this->quantity = $quantity;
    }
}
