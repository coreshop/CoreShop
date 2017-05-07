<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Order extends Base
{
    public function testOrderCreation()
    {
        $this->printTestName();

        /**
         * @var $invoice OrderInterface
         */
        $order = $this->getFactory('order')->createNew();

        $this->assertNotNull($order);
    }

    public function testCartToOrderTransformer()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var $order OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        $this->assertNotNull($order);
        $this->assertEquals(288, $order->getSubtotal());
    }
}
