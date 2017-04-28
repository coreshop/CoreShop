<?php

namespace CoreShop\Test\Models;

use CoreShop\Model\Configuration;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Product extends Base
{
    /**
     * Test Product Creation
     */
    public function testProductCreation()
    {
        $this->printTestName();

        $this->assertNotNull(Data::$product1);
    }

    /**
     * Test Product Price
     */
    public function testProductPrice()
    {
        $this->printTestName();

        $this->assertEquals(15 * 1.2, Data::$product1->getPrice());
    }

    /**
     * Test Product Tax
     */
    public function testProductTax()
    {
        $this->printTestName();

        $this->assertEquals(15 * 1.2 - 15, Data::$product1->getTaxAmount());
    }
}
