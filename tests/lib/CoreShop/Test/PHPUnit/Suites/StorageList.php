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

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\StorageList\Model\StorageListItem;
use CoreShop\Component\StorageList\SimpleStorageListModifier;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class StorageList extends Base
{
    /**
     * @return \CoreShop\Component\StorageList\Model\StorageList
     */
    protected function getStorageList()
    {
        return new \CoreShop\Component\StorageList\Model\StorageList();
    }

    /**
     * @return Factory
     */
    protected function getStorageListItemFactory()
    {
        return new Factory(StorageListItem::class);
    }

    /**
     * @return SimpleStorageListModifier
     */
    protected function getSimpleStorageListModifier()
    {
        return new SimpleStorageListModifier($this->getStorageListItemFactory());
    }

    /**
     * Test Cart Add Item.
     */
    public function testSimpleStorageList()
    {
        $this->printTestName();

        $list = $this->getStorageList();
        $modifier = $this->getSimpleStorageListModifier();

        $modifier->addItem($list, Data::$product1);
        $this->assertEquals(1, count($list->getItems()));

        $modifier->addItem($list, Data::$product1);
        $this->assertEquals(1, count($list->getItems()));

        $modifier->addItem($list, Data::$product2);
        $this->assertEquals(2, count($list->getItems()));

        $modifier->updateItemQuantity($list, Data::$product2, 0);
        $this->assertEquals(1, count($list->getItems()));
    }
}
