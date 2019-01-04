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

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Store extends Base
{
    /**
     * Test Shop Creation.
     */
    public function testStandardStoreCreation()
    {
        $this->printTestName();

        $this->assertNotNull($this->getRepository('store')->findStandard());
    }

    public function testStoreCreation()
    {
        $this->printTestName();

        /**
         * @var StoreInterface
         */
        $store = $this->getFactory('store')->createNew();

        $store->setName('test-country');
        $store->setTemplate('test');
        $store->setBaseCountry(Data::$store->getBaseCountry());
        $store->setCurrency(Data::$store->getCurrency());

        $this->assertNull($store->getId());

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();

        $this->assertNotNull($store->getId());

        $this->getEntityManager()->remove($store);
        $this->getEntityManager()->flush();
    }

    public function testStoreContext()
    {
        $this->printTestName();

        $this->assertEquals($this->get('coreshop.context.store')->getStore()->getId(), Data::$store->getId());
    }
}
