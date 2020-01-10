<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Order\Cart\CartModifierInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Cart extends Base
{
    /**
     * Test Cart Creation.
     */
    public function testCartCreation()
    {
        $this->printTestName();

        $cart = Data::createCart();

        $this->assertNotNull($cart);
    }

    /**
     * Test Cart Subtotal.
     */
    public function testCartSubtotal()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();

        $subtotal = $cart->getSubtotal();
        $tax = $cart->getSubtotalTax();
        $subTotalWT = $cart->getSubtotal(false);

        $this->assertEquals(28800, $subtotal);
        $this->assertEquals($subtotal - $subTotalWT, $tax);
        $this->assertEquals($subtotal - $tax, $subTotalWT);
    }

    /**
     * Test Cart Total.
     */
    public function testCartTotal()
    {
        $this->printTestName();

        //Disable Carrier for testing purposes, otherwise this would cause the default carrier resolver
        Data::$carrier1->removeStore(Data::$store);

        $this->getEntityManager()->persist(Data::$carrier1);
        $this->getEntityManager()->flush();

        $cart = Data::createCartWithProducts();

        $total = $cart->getTotal();
        $tax = $cart->getTotalTax();
        $totalWT = $total - $tax;

        $this->get('coreshop.cart.manager')->persistCart($cart);

        $this->assertEquals(28800, $cart->getSubtotal() + $cart->getShipping());
        $this->assertEquals(28800, $total);
        $this->assertEquals($total - $totalWT, $tax);
        $this->assertEquals($total - $tax, $totalWT);

        $cart->setCustomer(Data::$customer1);
        $cart->setInvoiceAddress(Data::$customer1->getAddresses()[0]);
        $cart->setShippingAddress(Data::$customer1->getAddresses()[0]);
        $cart->setCarrier(Data::$carrier1);

        $this->get('coreshop.cart.manager')->persistCart($cart);

        $this->assertEquals(1200, $cart->getShipping());
        $this->assertEquals(1000, $cart->getShipping(false));
        $this->assertEquals(30000, $cart->getTotal());

        Data::$carrier1->addStore(Data::$store);

        $this->getEntityManager()->persist(Data::$carrier1);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Cart Add Item.
     */
    public function testCartAddItem()
    {
        $this->printTestName();

        $cart = Data::createCart();

        /**
         * @var CartModifierInterface
         */
        $modifier = $this->get('coreshop.cart.modifier');

        $modifier->addItem($cart, Data::$product1);
        $this->assertEquals(1, count($cart->getItems()));

        $modifier->addItem($cart, Data::$product1);
        $this->assertEquals(1, count($cart->getItems()));

        $modifier->addItem($cart, Data::$product2);
        $this->assertEquals(2, count($cart->getItems()));
    }
}
