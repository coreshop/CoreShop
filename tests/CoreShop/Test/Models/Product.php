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

use CoreShop\Model\Configuration;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Product extends Base
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testProductCreation()
    {
        $this->printTestName();

        $this->assertNotNull(Data::$product1);
    }

    public function testProductPrice()
    {
        $this->printTestName();

        Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);

        $this->assertEquals(15 * 1.2, Data::$product1->getPrice());

        Configuration::set("SYSTEM.BASE.PRICES.GROSS", true);

        $this->assertEquals(15, Data::$product1->getPrice());
    }

    public function testProductTax()
    {
        $this->printTestName();

        Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);

        $this->assertEquals(15 * 1.2 - 15, Data::$product1->getTaxAmount());

        Configuration::set("SYSTEM.BASE.PRICES.GROSS", true);

        $this->assertEquals(15 - (15 / 1.2), Data::$product1->getTaxAmount());
    }

    public function testProductDeliveryPrice()
    {
        $this->printTestName();

        Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);

        $this->assertEquals(12, Data::$product1->getCheapestDeliveryPrice());
        $this->assertEquals(24, Data::$product2->getCheapestDeliveryPrice());
        $this->assertEquals(12, Data::$product3->getCheapestDeliveryPrice());
    }
}
