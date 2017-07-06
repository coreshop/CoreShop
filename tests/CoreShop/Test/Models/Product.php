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

        $this->assertEquals(1800, $this->getPriceCalculator()->getPrice(Data::$product1));
    }

    /**
     * Test Product Tax.
     */
    public function testProductTax()
    {
        $this->printTodoTestName();

        //$this->assertEquals(300, Data::$product1->getTaxAmount());
    }

    private function getPriceCalculator() {
        return $this->get('coreshop.product.taxed_price_calculator');
    }
}
