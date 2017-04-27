<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Order\Cart\CartModifierInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Cart extends Base
{
    /**
     * Test Cart Creation
     */
    public function testCartCreation()
    {
        $this->printTestName();

        $cart = Data::createCart();

        $this->assertNotNull($cart);
    }

    /**
     * Test Cart Subtotal
     */
    public function testCartSubtotal()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();

        $subtotal = $cart->getSubtotal();
        $tax = $cart->getSubtotalTax();
        $subTotalWT = $cart->getSubtotal(false);

        $this->assertEquals(288, $subtotal);
        $this->assertEquals($subtotal-$subTotalWT, $tax);
        $this->assertEquals($subtotal-$tax, $subTotalWT);
    }

    /**
     * Test Cart Total
     */
    public function testCartTotal()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();

        $total = $cart->getTotal();
        $tax = $cart->getTotalTax();
        $totalWT = $total - $tax;

        $this->assertEquals(288, $cart->getSubtotal() + $cart->getShipping());
        $this->assertEquals(288, $total);
        $this->assertEquals($total-$totalWT, $tax);
        $this->assertEquals($total-$tax, $totalWT);

        $cart->setCustomer(Data::$customer1);
        $cart->setInvoiceAddress(Data::$customer1->getAddresses()[0]);
        $cart->setShippingAddress(Data::$customer1->getAddresses()[0]);
        $cart->setCarrier(Data::$carrier1);

        $this->assertEquals(12, $cart->getShipping());
        $this->assertEquals(10, $cart->getShipping(false));
        $this->assertEquals(300, $cart->getTotal());
    }

    /**
     * Test Cart Add Item
     */
    public function testCartAddItem()
    {
        $this->printTestName();

        $cart = Data::createCart();

        /**
         * @var $modifier CartModifierInterface
         */
        $modifier = $this->get('coreshop.cart.modifier');

        $modifier->addCartItem($cart, Data::$product1);
        $this->assertEquals(1, count($cart->getItems()));

        $modifier->addCartItem($cart, Data::$product1);
        $this->assertEquals(1, count($cart->getItems()));

        $modifier->addCartItem($cart, Data::$product2);
        $this->assertEquals(2, count($cart->getItems()));
    }
}
