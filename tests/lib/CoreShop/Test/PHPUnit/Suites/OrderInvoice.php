<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class OrderInvoice extends Base
{
    public function testOrderInvoiceCreation()
    {
        $this->printTestName();

        /**
         * @var OrderInvoiceInterface
         */
        $invoice = $this->getFactory('order_invoice')->createNew();

        $this->assertNotNull($invoice);
    }

    public function testProcessableOrderItemInvoice()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        $processableItems = $this->get('coreshop.order.invoice.processable')->getProcessableItems($order);

        $this->assertNotNull($processableItems);
        $this->assertTrue(is_array($processableItems));
        $this->assertEquals(3, count($processableItems));
    }

    public function testOrderToInvoiceTransformer()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        $processableItems = $this->get('coreshop.order.invoice.processable')->getProcessableItems($order);

        /**
         * @var OrderInvoiceInterface
         */
        $invoice = $this->getFactory('order_invoice')->createNew();
        $invoice = $this->get('coreshop.order.transformer.order_to_invoice')->transform($order, $invoice, $processableItems);

        $this->assertEquals(28800, $invoice->getSubtotal());
    }
}
