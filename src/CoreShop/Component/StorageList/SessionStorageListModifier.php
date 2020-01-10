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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListProductInterface;

class SessionStorageListModifier extends SimpleStorageListModifier
{
    /**
     * @var StorageListManagerInterface
     */
    private $manager;

    /**
     * @param FactoryInterface            $storageListItemFactory
     * @param StorageListManagerInterface $manager
     */
    public function __construct(FactoryInterface $storageListItemFactory, StorageListManagerInterface $manager)
    {
        parent::__construct($storageListItemFactory);

        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function updateItemQuantity(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 0, $increaseAmount = false)
    {
        $item = parent::updateItemQuantity($storageList, $product, $quantity, $increaseAmount);

        $this->manager->persist($storageList);

        return $item;
    }
}
