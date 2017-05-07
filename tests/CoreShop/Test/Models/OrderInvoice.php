<?php

namespace CoreShop\Test\Models;

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
         * @var $invoice OrderInvoiceInterface
         */
        $invoice = $this->getFactory('order_invoice')->createNew();

        $this->assertNotNull($invoice);
    }

    public function testProcessableOrderItemInvoice()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var $invoice OrderInterface
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
         * @var $order OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        $processableItems = $this->get('coreshop.order.invoice.processable')->getProcessableItems($order);

        /**
         * @var $invoice OrderInvoiceInterface
         */
        $invoice = $this->getFactory('order_invoice')->createNew();
        $invoice = $this->get('coreshop.order.transformer.order_to_invoice')->transform($order, $invoice, $processableItems);

        $this->assertEquals(288, $invoice->getSubtotal());
    }
}
