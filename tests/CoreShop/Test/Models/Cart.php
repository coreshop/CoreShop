<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\Models;

use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Cart extends Base
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCartCreation() {
        $this->printTestName();

        $cart = Data::createCart();

        $this->assertNotNull($cart);
    }

    public function testCartDeliveryPrice() {
        $this->printTestName();

        $cart1 = Data::createCart();
        $cart1->addItem(Data::$product1);

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $cart3 = Data::createCart();
        $cart3->addItem(Data::$product1);
        $cart3->addItem(Data::$product2);

        $this->assertEquals(12, $cart1->getShipping());
        $this->assertEquals(24, $cart2->getShipping());
        $this->assertEquals(24, $cart3->getShipping());
    }

    public function testCartSubtotal() {
        $this->printTestName();

        $cart = Data::createCartWithProducts();

        $subtotal = $cart->getSubtotal();
        $tax = $cart->getSubtotalTax();
        $subTotalWT = $cart->getSubtotal(false);

        $this->assertEquals(243, $subtotal);
        $this->assertEquals($subtotal-$subTotalWT, $tax);
        $this->assertEquals($subtotal-$tax, $subTotalWT);
    }

    public function testCartTotal() {
        $this->printTestName();

        $cart = Data::createCartWithProducts();

        $total = $cart->getTotal();
        $tax = $cart->getTotalTax();
        $totalWT = $total - $tax;

        $this->assertEquals(267, $cart->getSubtotal() + $cart->getShipping());
        $this->assertEquals(267, $total);
        $this->assertEquals($total-$totalWT, $tax);
        $this->assertEquals($total-$tax, $totalWT);
    }

    public function testCartAddItem() {
        $this->printTestName();

        $cart = Data::createCart();

        $cart->addItem(Data::$product1);
        $this->assertEquals(1, count($cart->getItems()));

        $cart->addItem(Data::$product1, 2);
        $this->assertEquals(1, count($cart->getItems()));

        $cart->addItem(Data::$product2, 2);
        $this->assertEquals(2, count($cart->getItems()));
    }
}
