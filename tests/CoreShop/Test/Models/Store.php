<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Store extends Base
{
    /**
     * Test Shop Creation
     */
    public function testStandardStoreCreation()
    {
        $this->printTestName();

        $this->assertNotNull($this->getRepository('store')->findStandard());
    }

    public function testStoreCreation() {
        $this->printTestName();

        /**
         * @var $store StoreInterface
         */
        $store = $this->getFactory('store')->createNew();

        $store->setName('test-country');
        $store->setTemplate('test');
        $store->setBaseCountry(Data::$store->getBaseCountry());
        $store->setBaseCurrency(Data::$store->getBaseCurrency());

        $this->assertNull($store->getId());

        $this->getEntityManager()->persist($store);
        $this->getEntityManager()->flush();

        $this->assertNotNull($store->getId());

        $this->getEntityManager()->remove($store);
        $this->getEntityManager()->flush();
    }

    public function testStoreContext() {
        $this->printTestName();

        $this->assertEquals($this->get('coreshop.context.store')->getStore()->getId(), Data::$store->getId());
    }
}
