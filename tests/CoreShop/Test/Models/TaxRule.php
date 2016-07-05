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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\Models;

use CoreShop\Model\TaxCalculator;
use CoreShop\Test\Base;

class TaxRule extends Base
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testTaxCreation()
    {
        $this->printTestName();

        $this->assertNotNull(\CoreShop\Model\TaxRuleGroup::getById(1));
        $this->assertNotNull(\CoreShop\Model\TaxRule::getById(1));
    }

    public function testTaxCalculator()
    {
        $this->printTestName();

        $tax10 = new \CoreShop\Model\Tax();
        $tax10->setRate(10);

        $tax20 = new \CoreShop\Model\Tax();
        $tax20->setRate(20);

        $taxCalculator = new TaxCalculator(array($tax10), TaxCalculator::DISABLE_METHOD);

        $this->assertEquals(11, $taxCalculator->addTaxes(10));
        $this->assertEquals(13.2, $taxCalculator->addTaxes(12));

        $taxCalculator = new TaxCalculator(array($tax10, $tax20), TaxCalculator::COMBINE_METHOD);

        $this->assertEquals(13, $taxCalculator->addTaxes(10));
        $this->assertEquals(15.6, $taxCalculator->addTaxes(12));

        $taxCalculator = new TaxCalculator(array($tax10, $tax20), TaxCalculator::ONE_AFTER_ANOTHER_METHOD);

        $this->assertEquals(13.2, round($taxCalculator->addTaxes(10), 2));
        $this->assertEquals(15.84, round($taxCalculator->addTaxes(12), 2));
    }
}
