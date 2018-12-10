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

namespace CoreShop\Test\PHPUnit\Suites\Product;

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

    /**
     * @param $index
     *
     * @return FilterInterface
     */
    private function createFilter($index)
    {
        /**
         * @var FilterInterface
         */
        $filter = $this->getFactory('filter')->createNew();
        $filter->setName('testFilter');
        $filter->setIndex($index);

        return $filter;
    }

    public function testFilterCreation()
    {
        $this->printTestName();

        /**
         * @var IndexInterface
         */
        $index = $this->createIndex();
        /**
         * @var FilterInterface
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

    public function testFilterService()
    {
        $this->printTestName();

        /**
         * @var IndexInterface
         */
        $index = $this->createIndex();
        /**
         * @var FilterInterface
         */
        $filter = $this->createFilter($index);

        $this->getEntityManager()->persist($index);
        $this->getEntityManager()->persist($filter);
        $this->getEntityManager()->flush();

        $parameter = new ParameterBag();

        /**
         * @var ListingInterface
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
