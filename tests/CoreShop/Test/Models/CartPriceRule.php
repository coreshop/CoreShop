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

namespace CoreShop\Test\Models;

use CoreShop\Model\PriceRule\Action\DiscountAmount;
use CoreShop\Model\PriceRule\Action\DiscountPercent;
use CoreShop\Model\PriceRule\Action\FreeShipping;
use CoreShop\Model\PriceRule\Action\Gift;
use CoreShop\Model\PriceRule\Condition\Countries;
use CoreShop\Model\PriceRule\Condition\CustomerGroups;
use CoreShop\Model\PriceRule\Condition\Customers;
use CoreShop\Model\PriceRule\Condition\TimeSpan;
use CoreShop\Model\PriceRule\Condition\Amount;
use CoreShop\Model\PriceRule\Condition\TotalPerCustomer;
use CoreShop\Model\PriceRule\Condition\Zones;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

/**
 * Class CartPriceRule
 * @package CoreShop\Test\Models
 */
class CartPriceRule extends Base
{
    /**
     * @var \CoreShop\Model\Cart\PriceRule
     */
    protected $priceRule;

    /**
     * Setup Test
     */
    public function setUp()
    {
        parent::setUp();

        $priceRule = \CoreShop\Model\Cart\PriceRule::create();
        $priceRule->setName("test-rule");
        $priceRule->setActive(true);
        $priceRule->setHighlight(false);
        $priceRule->setCode("");
        $priceRule->setLabel("test-rule");
        $priceRule->setDescription("");

        $this->priceRule = $priceRule;
    }

    /**
     * Test Price Rule Condition Customer
     */
    public function testPriceRuleCondCustomer()
    {
        $customerConditon = new Customers();
        $customerConditon->setCustomers([Data::$customer1->getId()]);

        $this->priceRule->setConditions([
            $customerConditon
        ]);

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customerConditon->checkConditionCart($cart, $this->priceRule));
    }

    /**
     * Test Price Rule Condition Time Span
     */
    public function testPriceRuleCondTimeSpan()
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

        $this->assertTrue($timeSpan->checkConditionCart($cart, $this->priceRule));

        $timeSpan->setDateFrom($yesterday->getTimestamp() * 1000);
        $timeSpan->setDateTo($yesterday->getTimestamp() * 1000);

        $this->assertFalse($timeSpan->checkConditionCart($cart, $this->priceRule));
    }

    /**
     * Test Price Rule Condition Amount
     */
    public function testPriceRuleCondAmount()
    {
        $amount = new Amount();
        $amount->setMinAmount(2);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($amount->checkConditionCart($cart, $this->priceRule));

        $amount->setMinAmount(10000);

        $this->assertFalse($amount->checkConditionCart($cart, $this->priceRule));
    }

    /**
     * Test Price Rule Condition Total Per Customer
     */
    public function testPriceRuleCondTotalPerCustomer()
    {
        $total = new TotalPerCustomer();
        $total->setTotal(1);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($total->checkConditionCart($cart, $this->priceRule));

        //@todo: create order an test pricerule again with assertFalse result
    }

    /**
     * Test Price Rule Condition Country
     */
    public function testPriceRuleCondCountry()
    {
        $country = new Countries();
        $country->setCountries([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($country->checkConditionCart($cart, $this->priceRule));

        $country->setCountries([1]);

        $this->assertFalse($country->checkConditionCart($cart, $this->priceRule));
    }

    /**
     * Test Price Rule Condition Zone
     */
    public function testPriceRuleCondZone()
    {
        $zone = new Zones();
        $zone->setZones([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($zone->checkConditionCart($cart, $this->priceRule));

        $zone->setZones([1]);

        $this->assertFalse($zone->checkConditionCart($cart, $this->priceRule));
    }

    /**
     * Test Price Rule Condition Category
     */
    public function testPriceRuleCondCategory()
    {
        //TODO: implement me
    }

    /**
     * Test Price Rule Condition Customer Group
     */
    public function testPriceRuleCondCustomerGroup()
    {
        $customer = new CustomerGroups();
        $customer->setCustomerGroups([Data::$customerGroup1->getId()]);

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customer->checkConditionCart($cart, $this->priceRule));

        $customer->setCustomerGroups([Data::$customerGroup2->getId()]);

        $this->assertFalse($customer->checkConditionCart($cart, $this->priceRule));
    }

    /**
     * Test Price Rule Action Gift
     */
    public function testPriceRuleActionGift()
    {
        $gift = new Gift();
        $gift->setGift(Data::$product1->getId());

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);

        $this->priceRule->setActions([$gift]);

        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $this->assertEquals(150 + 24, $cart->getTotal());
        $this->assertEquals(Data::$product1->getPrice(), $cart->getDiscount());
    }

    /**
     * Test Price Rule Action Free Shipping
     */
    public function testPriceRuleActionFreeShipping()
    {
        $freeShipping = new FreeShipping();

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);

        $this->priceRule->setActions([$freeShipping]);

        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $this->assertEquals(0, $cart->getShipping());
    }

    /**
     * Test Price Rule Action Discount Amount
     */
    public function testPriceRuleActionDiscountAmount()
    {
        $discount = new DiscountAmount();
        $discount->setAmount(10);

        $this->priceRule->setActions([$discount]);

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);
        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $this->assertEquals($cart2->getTotal() - 10, $cart->getTotal());
    }

    /**
     * Test Price Rule Action Percent
     */
    public function testPriceRuleActionDiscountPercent()
    {
        $discount = new DiscountPercent();
        $discount->setPercent(10);

        $this->priceRule->setActions([$discount]);

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);
        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $this->assertEquals($cart2->getSubtotal() * 0.1, $this->priceRule->getDiscount($cart));
    }
}
