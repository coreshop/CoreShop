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
        $this->printTodoTestName();
        //TODO

        /*$this->assertNotNull(Data::$product1);*/
    }

    /**
     * Test Product Price
     */
    public function testProductPrice()
    {
        $this->printTodoTestName();
        //TODO

        /*Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);

        $this->assertEquals(15 * 1.2, Data::$product1->getPrice());

        Configuration::set("SYSTEM.BASE.PRICES.GROSS", true);

        $this->assertEquals(15, Data::$product1->getPrice());*/
    }

    /**
     * Test Product Tax
     */
    public function testProductTax()
    {
        $this->printTodoTestName();
        //TODO

        /*Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);

        $this->assertEquals(15 * 1.2 - 15, Data::$product1->getTaxAmount());

        Configuration::set("SYSTEM.BASE.PRICES.GROSS", true);

        $this->assertEquals(15 - (15 / 1.2), Data::$product1->getTaxAmount());*/
    }

    /**
     * Test Product Delivery Price
     */
    public function testProductDeliveryPrice()
    {
        $this->printTodoTestName();
        //TODO

        /*Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);

        $this->assertEquals(12, Data::$product1->getCheapestDeliveryPrice());
        $this->assertEquals(24, Data::$product2->getCheapestDeliveryPrice());
        $this->assertEquals(12, Data::$product3->getCheapestDeliveryPrice());*/
    }
}
