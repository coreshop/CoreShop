<?php

namespace CoreShop\Test\Models;

use CoreShop\Model\Carrier\DeliveryPrice;
use CoreShop\Model\Carrier\RangePrice;
use CoreShop\Model\Carrier\RangeWeight;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Carrier extends Base
{
    /**
     * Test Carrier Creation
     */
    public function testCarrierCreation()
    {
        $this->printTodoTestName();

        //TODO
    }

    /**
     * Test Carrier Price
     */
    public function testCarrierPrice()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCartWithProducts();

        $price1 = Data::$carrier1->getDeliveryPrice($cart, true);
        $price2 = Data::$carrier2->getDeliveryPrice($cart, true);

        $this->assertEquals(false, $price1);
        $this->assertEquals(24, $price2);*/
    }

    /**
     * Test Carrier Taxes
     */
    public function testCarrierTax()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCartWithProducts();

        $tax = Data::$carrier2->getTaxAmount($cart);

        $this->assertEquals(4, $tax);*/
        //TODO
    }

    /**
     * Test Carrier Carts
     */
    public function testCarriersForCart()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCartWithProducts();
        $carriersForCart = \CoreShop\Model\Carrier::getCarriersForCart($cart);

        $this->assertEquals(1, count($carriersForCart));
        $this->assertEquals(Data::$carrier2->getId(), $carriersForCart[0]->getId());*/
        //TODO
    }
}
