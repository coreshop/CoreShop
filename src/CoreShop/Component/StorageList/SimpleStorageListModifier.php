<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\StorageList;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\Model\StorageListProductInterface;

class SimpleStorageListModifier implements StorageListModifierInterface
{
    /**
     * @var FactoryInterface
     */
    private $storageListItemFactory;

    /**
     * @param FactoryInterface $storageListItemFactory
     */
    public function __construct(FactoryInterface $storageListItemFactory)
    {
        $this->storageListItemFactory = $storageListItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 1)
    {
        return $this->updateItemQuantity($storageList, $product, $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(StorageListInterface $storageList, StorageListItemInterface $item)
    {
        return $this->updateItemQuantity($storageList, $item->getProduct(), 0);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItemQuantity(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 0, $increaseAmount = false)
    {
        $item = $storageList->getItemForProduct($product);

        if ($item instanceof StorageListItemInterface) {
            if ($quantity <= 0) {
                $storageList->removeItem($item);

                return false;
            }

            $newQuantity = $quantity;

            if ($increaseAmount) {
                $currentQuantity = $item->getQuantity();

                if (is_int($currentQuantity)) {
                    $newQuantity = $currentQuantity + $quantity;
                }
            }

            $item->setQuantity($newQuantity);
        } else {
            /**
             * @var StorageListItemInterface $item
             */
            $item = $this->storageListItemFactory->createNew();
            $item->setProduct($product);
            $item->setQuantity(1);

            $storageList->addItem($item);
        }

        return $item;
    }
}
