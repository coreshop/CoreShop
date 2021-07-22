<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\PHPUnit\Suites\Product;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Test\Base;

class Index extends Base
{
    /**
     * @return IndexInterface
     */
    private function createIndex()
    {
        /**
         * @var IndexInterface
         */
        $index = $this->getFactory('index')->createNew();
        $index->setName('mysql_test');
        $index->setWorker('mysql');
        $index->setConfiguration([]);

        return $index;
    }

    public function testIndexCreation()
    {
        $this->printTestName();

        $index = $this->createIndex();

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->flush();

        $this->assertNotNull($index->getId());

        $this->getEntityManager()->remove($index);
        $this->getEntityManager()->flush();

        $this->assertNull($index->getId());
    }

    public function testIndexListing()
    {
        $this->printTestName();

        $index = $this->createIndex();

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->flush();

        $workerServiceRegistry = $this->get('coreshop.registry.index.worker');

        /**
         * @var WorkerInterface
         */
        $worker = $workerServiceRegistry->get($index->getWorker());
        $list = $worker->getList($index);

        $this->assertInstanceOf(ListingInterface::class, $list);
    }
}
