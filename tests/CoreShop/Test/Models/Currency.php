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
use CoreShop\Tool;

class Currency extends Base
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCurrencyCreation() {
        $this->printTestName();

        $this->assertNotNull(\CoreShop\Model\Currency::getById(1));
    }

    public function testCurrencyConversion() {
        $usd = \CoreShop\Model\Currency::getByName("US Dollars");
        $euro = \CoreShop\Model\Currency::getByName("Euro");
        $asd = \CoreShop\Model\Currency::getByName("Australian Dollars");

        $usd->setExchangeRate(1.2);
        $asd->setExchangeRate(2);

        $this->assertEquals(12, Tool::convertToCurrency(10, $usd, $euro));
        $this->assertEquals(20, Tool::convertToCurrency(10, $asd, $euro));

    }
}
