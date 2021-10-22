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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class SessionStorageListModifier extends SimpleStorageListModifier
{
    public function __construct(private StorageListManagerInterface $manager)
    {
        parent::__construct();
    }

    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        parent::addToList($storageList, $item);

        $this->manager->persist($storageList);
    }

    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        parent::removeFromList($storageList, $item);

        $this->manager->persist($storageList);
    }
}
