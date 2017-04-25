<?php

namespace CoreShop\Test\Models;

use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Cart extends Base
{
    /**
     * Test Cart Creation
     */
    public function testCartCreation()
    {
        $this->printTodoTestName();

        /*
        $cart = Data::createCart();

        $this->assertNotNull($cart);*/
        //TODO
    }

    /**
     * Test Cart Delivery Price
     */
    public function testCartDeliveryPrice()
    {
        $this->printTodoTestName();

        /*$cart1 = Data::createCart();
        $cart1->addItem(Data::$product1);

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $cart3 = Data::createCart();
        $cart3->addItem(Data::$product1);
        $cart3->addItem(Data::$product2);

        $this->assertEquals(12, $cart1->getShipping());
        $this->assertEquals(24, $cart2->getShipping());
        $this->assertEquals(24, $cart3->getShipping());*/
        //TODO
    }

    /**
     * Test Cart Subtotal
     */
    public function testCartSubtotal()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCartWithProducts();

        $subtotal = $cart->getSubtotal();
        $tax = $cart->getSubtotalTax();
        $subTotalWT = $cart->getSubtotal(false);

        $this->assertEquals(243, $subtotal);
        $this->assertEquals($subtotal-$subTotalWT, $tax);
        $this->assertEquals($subtotal-$tax, $subTotalWT);*/
        //TODO
    }

    /**
     * Test Cart Total
     */
    public function testCartTotal()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCartWithProducts();

        $total = $cart->getTotal();
        $tax = $cart->getTotalTax();
        $totalWT = $total - $tax;

        $this->assertEquals(267, $cart->getSubtotal() + $cart->getShipping());
        $this->assertEquals(267, $total);
        $this->assertEquals($total-$totalWT, $tax);
        $this->assertEquals($total-$tax, $totalWT);*/
        //TODO
    }

    /**
     * Test Cart Add Item
     */
    public function testCartAddItem()
    {
        $this->printTodoTestName();

        /*$cart = Data::createCart();

        $cart->addItem(Data::$product1);
        $this->assertEquals(1, count($cart->getItems()));

        $cart->addItem(Data::$product1, 2);
        $this->assertEquals(1, count($cart->getItems()));

        $cart->addItem(Data::$product2, 2);
        $this->assertEquals(2, count($cart->getItems()));*/
        //TODO
    }
}
