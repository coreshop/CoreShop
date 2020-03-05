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

use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class SessionStorageListModifier extends SimpleStorageListModifier
{
    private $manager;

    public function __construct(StorageListManagerInterface $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        $result = parent::addToList($storageList, $item);

        $this->manager->persist($storageList);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        $result = parent::removeFromList($storageList, $item);

        $this->manager->persist($storageList);
    }
}
