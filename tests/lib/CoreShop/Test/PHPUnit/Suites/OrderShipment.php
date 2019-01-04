<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class OrderShipment extends Base
{
    public function testOrderShipmentCreation()
    {
        $this->printTestName();

        /**
         * @var OrderShipmentInterface
         */
        $shipment = $this->getFactory('order_shipment')->createNew();

        $this->assertNotNull($shipment);
    }

    public function testProcessableOrderItemShipment()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        $processableItems = $this->get('coreshop.order.shipment.processable')->getProcessableItems($order);

        $this->assertNotNull($processableItems);
        $this->assertTrue(is_array($processableItems));
        $this->assertEquals(3, count($processableItems));
    }

    public function testOrderToShipmentTransformer()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        $processableItems = $this->get('coreshop.order.shipment.processable')->getProcessableItems($order);

        /**
         * @var OrderShipmentInterface
         */
        $shipment = $this->getFactory('order_shipment')->createNew();
        $shipment = $this->get('coreshop.order.transformer.order_to_shipment')->transform($order, $shipment, $processableItems);

        $this->assertEquals(3, count($shipment->getItems()));
    }
}
