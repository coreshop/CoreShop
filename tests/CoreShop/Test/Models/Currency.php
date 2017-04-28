<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Currency extends Base
{
    /**
     * Test Currency Creation
     */
    public function testCurrencyCreation()
    {
        $this->printTestName();

        /**
         * @var $currency CurrencyInterface
         */
        $currency = $this->getFactory('currency')->createNew();

        $currency->setName('test-country');
        $currency->setIsoCode('TEC');

        $this->assertNull($currency->getId());

        $this->getEntityManager()->persist($currency);
        $this->getEntityManager()->flush();

        $this->assertNotNull($currency->getId());
    }

    public function testCurrencyContext() {
        $this->printTestName();

        $this->assertEquals($this->get('coreshop.context.currency')->getCurrency()->getId(), Data::$store->getBaseCurrency()->getId());
    }
}
