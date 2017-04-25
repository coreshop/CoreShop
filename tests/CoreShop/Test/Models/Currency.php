<?php

namespace CoreShop\Test\Models;

use CoreShop\Test\Base;

class Currency extends Base
{
    /**
     * Test Currency Creation
     */
    public function testCurrencyCreation()
    {
        $this->printTodoTestName();
        //TODO

        /*$this->assertNotNull(\CoreShop\Model\Currency::getById(1));*/
    }

    /**
     * Test Currency Conversion
     */
    public function testCurrencyConversion()
    {
        $this->printTodoTestName();
        //TODO

        /*$usd = \CoreShop\Model\Currency::getByName("US Dollars");
        $euro = \CoreShop\Model\Currency::getByName("Euro");
        $asd = \CoreShop\Model\Currency::getByName("Australian Dollars");

        $usd->setExchangeRate(1.2);
        $asd->setExchangeRate(2);

        $this->assertEquals(12, \CoreShop::getTools()->convertToCurrency(10, $usd, $euro));
        $this->assertEquals(20, \CoreShop::getTools()->convertToCurrency(10, $asd, $euro));*/
    }
}
