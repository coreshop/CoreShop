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
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Order extends Base
{
    public function testOrderCreation()
    {
        $this->printTestName();

        /**
         * @var OrderInterface
         */
        $order = $this->getFactory('order')->createNew();

        $this->assertNotNull($order);
    }

    public function testCartToOrderTransformer()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        $this->assertNotNull($order);
        $this->assertEquals(28800, $order->getSubtotal());
    }
}
