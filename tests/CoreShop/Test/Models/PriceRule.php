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

use CoreShop\Model\PriceRule\Condition\Customer;
use CoreShop\Model\PriceRule\Condition\TimeSpan;
use CoreShop\Model\PriceRule\Condition\Amount;
use CoreShop\Model\PriceRule\Condition\Category as ConditionCategory;
use CoreShop\Model\PriceRule\Condition\Country as ConditionCountry;
use CoreShop\Model\PriceRule\Condition\CustomerGroup as ConditionCustomerGroup;
use CoreShop\Model\PriceRule\Condition\Product as ConditionProduct;
use CoreShop\Model\PriceRule\Condition\TotalAvailable;
use CoreShop\Model\PriceRule\Condition\TotalPerCustomer;
use CoreShop\Model\PriceRule\Condition\Zone as ConditionZone;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class PriceRule extends Base
{
    protected $priceRule;

    public function setUp()
    {
        parent::setUp();

        $priceRule = new \CoreShop\Model\PriceRule();
        $priceRule->setName("test-rule");
        $priceRule->setActive(true);

        $this->priceRule = $priceRule;
    }

    public function testPriceRuleCondCustomer() {
        $customerConditon = new Customer();
        $customerConditon->setCustomer(Data::$customer1->getId());

        $this->priceRule->setConditions(array(
            $customerConditon
        ));

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customerConditon->checkCondition($cart, $this->priceRule));
    }

    public function testPriceRuleCondTimeSpan() {
        $today              = strtotime('12:00:00');
        $yesterday          = strtotime('-1 day', $today);
        $tomorrow          = strtotime('1 day', $today);

        $timeSpan = new TimeSpan();
        $timeSpan->setDateFrom(new \Zend_Date($yesterday));
        $timeSpan->setDateTo(new \Zend_Date($tomorrow));

        $cart = Data::createCartWithProducts();

        $this->assertTrue($timeSpan->checkCondition($cart, $this->priceRule));

        $timeSpan->setDateFrom($yesterday);
        $timeSpan->setDateTo($yesterday);

        $this->assertFalse($timeSpan->checkCondition($cart, $this->priceRule));
    }

    public function testPriceRuleCondAmount() {

    }

    public function testPriceRuleCondTotalAvailable() {

    }

    public function testPriceRuleCondTotalPerCustomer() {

    }

    public function testPriceRuleCondCountry() {

    }

    public function testPriceRuleCondZone() {

    }

    public function testPriceRuleCondCategory() {

    }

    public function testPriceRuleCondCustomerGroup() {

    }

    public function testPriceRuleActionGift() {

    }

    public function testPriceRuleActionFreeShipping() {

    }

    public function testPriceRuleActionDiscountAmount() {

    }

    public function testPriceRuleActionDiscountPercent() {

    }
}
