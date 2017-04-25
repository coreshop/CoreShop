<?php

namespace CoreShop\Test\Models\Product;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Test\Base;

class Index extends Base
{
    /**
     * @return IndexInterface
     */
    private function createIndex() {
        /**
         * @var $index IndexInterface
         */
        $index = $this->getFactory('index')->createNew();
        $index->setName('mysql_test');
        $index->setWorker('mysql');
        $index->setConfiguration([]);

        return $index;
    }

    public function testIndexCreation() {
        $this->printTestName();

        $index = $this->createIndex();

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->flush();

        $this->assertNotNull($index->getId());

        $this->getEntityManager()->remove($index);
        $this->getEntityManager()->flush();

        $this->assertNull($index->getId());
    }

    public function testIndexWorkerMysql() {
        $this->printTestName();

        $index = $this->createIndex();

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->flush();

        $workerServiceRegistry = $this->get('coreshop.registry.index.worker');

        /**
         * @var $worker WorkerInterface
         */
        $worker = $workerServiceRegistry->get($index->getWorker());
        $worker->createOrUpdateIndexStructures($index);
    }

    public function testIndexListing() {
        $this->printTestName();

        $index = $this->createIndex();

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->flush();

        $workerServiceRegistry = $this->get('coreshop.registry.index.worker');

        /**
         * @var $worker WorkerInterface
         */
        $worker = $workerServiceRegistry->get($index->getWorker());
        $list = $worker->getList($index);

        $this->assertInstanceOf(ListingInterface::class, $list);
    }
}
