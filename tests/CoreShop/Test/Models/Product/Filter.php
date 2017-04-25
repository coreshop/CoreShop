<?php

namespace CoreShop\Test\Models\Product;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Test\Base;
use Symfony\Component\HttpFoundation\ParameterBag;

class Filter extends Base
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

    /**
     * @param $index
     * @return FilterInterface
     */
    private function createFilter($index) {
        /**
         * @var $filter FilterInterface
         */
        $filter = $this->getFactory('filter')->createNew();
        $filter->setName('testFilter');
        $filter->setIndex($index);

        return $filter;
    }

    public function testFilterCreation() {
        $this->printTestName();

        /**
         * @var $index IndexInterface
         */
        $index = $this->createIndex();
        /**
         * @var $filter FilterInterface
         */
        $filter = $this->createFilter($index);

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->persist($filter);
        $this->getEntityManager()->flush();

        $this->assertNotNull($filter->getId());
        $this->assertEmpty($filter->getConditions());
        $this->assertEmpty($filter->getPreConditions());
        $this->assertSame($filter->getIndex()->getId(), $index->getId());

        $this->getEntityManager()->remove($filter);
        $this->getEntityManager()->remove($index);
        $this->getEntityManager()->flush();

        $this->assertNull($filter->getId());
    }

    public function testFilterService() {
        $this->printTestName();

        /**
         * @var $index IndexInterface
         */
        $index = $this->createIndex();
        /**
         * @var $filter FilterInterface
         */
        $filter = $this->createFilter($index);

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->persist($filter);
        $this->getEntityManager()->flush();

        $parameter = new ParameterBag();

        /**
         * @var $filteredList ListingInterface
         */
        $filteredList = $this->get('coreshop.factory.filter.list')->createList($filter, $parameter);
        $filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);

        $currentFilter = $this->get('coreshop.filter.processor')->processConditions($filter, $filteredList, $parameter);
        $preparedConditions = $this->get('coreshop.filter.processor')->prepareConditionsForRendering($filter, $filteredList, $currentFilter);

        $this->assertInstanceOf(ListingInterface::class, $filteredList);
        $this->assertEmpty($currentFilter);
        $this->assertEmpty($preparedConditions);
    }
}
