<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\OrderItem as BaseOrderItem;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

abstract class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->getTotalWeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getDigitalProduct()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDigitalProduct($digitalProduct)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultUnitQuantity()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultUnitQuantity($defaultUnitQuantity)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalWeight()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalWeight($totalWeight)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemWeight()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemWeight($itemWeight)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }


    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getWidth() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getHeight() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getDepth()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getDepth() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitDefinition()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitDefinition($productUnitDefinition)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnitDefinition()
    {
        return $this->getUnitDefinition() instanceof ProductUnitDefinitionInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(StorageListItemInterface $storageListItem)
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
