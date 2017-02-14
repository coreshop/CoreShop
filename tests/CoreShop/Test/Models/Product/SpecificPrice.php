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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\Models\Product;

use CoreShop\Model\PriceRule\Condition\Countries;
use CoreShop\Model\PriceRule\Condition\Customers;
use CoreShop\Model\PriceRule\Condition\Quantity;
use CoreShop\Model\PriceRule\Condition\TimeSpan;
use CoreShop\Model\PriceRule\Condition\Zones;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

/**
 * Class SpecificPrice
 * @package CoreShop\Test\Models\Product
 */
class SpecificPrice extends Base
{
    /**
     * @var \CoreShop\Model\Product\SpecificPrice
     */
    protected $specificPrice;

    /**
     * @var \CoreShop\Model\Product
     */
    protected $product;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();

        $specificPrice = \CoreShop\Model\Product\SpecificPrice::create();
        $specificPrice->setName("test-rule");

        $this->specificPrice = $specificPrice;
        $this->product = Data::$product1;
    }

    /**
     * Test Price Rule Quantity
     */
    public function testPriceRuleQuantity()
    {
        $quantityCondition = new Quantity();
        $quantityCondition->setMinQuantity(4);
        $quantityCondition->setMaxQuantity(10);

        $this->specificPrice->setConditions([
            $quantityCondition
        ]);

        \CoreShop::getTools()->getCart()->addItem($this->product, 4);

        $this->assertTrue($quantityCondition->checkConditionProduct($this->product, $this->specificPrice));
    }

    /**
     * Test Price Rule Customer
     */
    public function testPriceRuleCustomer()
    {
        $customerConditon = new Customers();
        $customerConditon->setCustomers([Data::$customer1->getId()]);

        $this->specificPrice->setConditions([
            $customerConditon
        ]);

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customerConditon->checkConditionProduct($this->product, $this->specificPrice));
    }

    /**
     * Test Price Rule Time Span
     */
    public function testPriceRuleTimeSpan()
    {
        $today = strtotime('12:00:00');
        $yesterday = strtotime('-1 day', $today);
        $tomorrow = strtotime('1 day', $today);

        $yesterday = new \Zend_Date($yesterday);
        $tomorrow = new \Zend_Date($tomorrow);

        $timeSpan = new TimeSpan();
        $timeSpan->setDateFrom($yesterday->getTimestamp() * 1000);
        $timeSpan->setDateTo($tomorrow->getTimestamp() * 1000);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($timeSpan->checkConditionProduct($this->product, $this->specificPrice));

        $timeSpan->setDateFrom($yesterday->getTimestamp() * 1000);
        $timeSpan->setDateTo($yesterday->getTimestamp() * 1000);

        $this->assertFalse($timeSpan->checkConditionProduct($this->product, $this->specificPrice));
    }

    /**
     * Test Price Rule Country
     */
    public function testPriceRuleCountry()
    {
        $country = new Countries();
        $country->setCountries([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($country->checkConditionProduct($this->product, $this->specificPrice));

        $country->setCountries([1]);

        $this->assertFalse($country->checkConditionProduct($this->product, $this->specificPrice));
    }

    /**
     * Test Price Rule Customer Group
     */
    public function testPriceRuleCustomerGroup()
    {
        $customerConditon = new Customers();
        $customerConditon->setCustomers([Data::$customer1->getId()]);

        $this->specificPrice->setConditions([
            $customerConditon
        ]);

        $this->assertTrue($customerConditon->checkConditionProduct($this->product, $this->specificPrice));
    }

    /**
     * Test Price Rule Zone
     */
    public function testPriceRuleZone()
    {
        $zone = new Zones();
        $zone->setZones([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($zone->checkConditionProduct($this->product, $this->specificPrice));

        $zone->setZones([1]);

        $this->assertFalse($zone->checkConditionProduct($this->product, $this->specificPrice));
    }
}
