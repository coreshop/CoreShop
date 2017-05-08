<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Test\Models;

use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Product extends Base
{
    /**
     * Test Product Creation.
     */
    public function testProductCreation()
    {
        $this->printTestName();

        $this->assertNotNull(Data::$product1);
    }

    /**
     * Test Product Price.
     */
    public function testProductPrice()
    {
        $this->printTestName();

        $this->assertEquals(15 * 1.2, Data::$product1->getPrice());
    }

    /**
     * Test Product Tax.
     */
    public function testProductTax()
    {
        $this->printTestName();

        $this->assertEquals(15 * 1.2 - 15, Data::$product1->getTaxAmount());
    }
}
