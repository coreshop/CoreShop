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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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

class CartPriceRule extends Base
{
    /**
     * @var \CoreShop\Model\Cart\PriceRule
     */
    protected $priceRule;

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

    public function testPriceRuleCondCustomer()
    {
        $customerConditon = new Customers();
        $customerConditon->setCustomers([Data::$customer1->getId()]);

        $this->priceRule->setConditions(array(
            $customerConditon
        ));

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customerConditon->checkConditionCart($cart, $this->priceRule));
    }

    public function testPriceRuleCondTimeSpan()
    {
        $today              = strtotime('12:00:00');
        $yesterday          = strtotime('-1 day', $today);
        $tomorrow          = strtotime('1 day', $today);

        $timeSpan = new TimeSpan();
        $timeSpan->setDateFrom(new \Zend_Date($yesterday));
        $timeSpan->setDateTo(new \Zend_Date($tomorrow));

        $cart = Data::createCartWithProducts();

        $this->assertTrue($timeSpan->checkConditionCart($cart, $this->priceRule));

        $timeSpan->setDateFrom($yesterday);
        $timeSpan->setDateTo($yesterday);

        $this->assertFalse($timeSpan->checkConditionCart($cart, $this->priceRule));
    }

    public function testPriceRuleCondAmount()
    {
        $amount = new Amount();
        $amount->setMinAmount(2);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($amount->checkConditionCart($cart, $this->priceRule));

        $amount->setMinAmount(10000);

        $this->assertFalse($amount->checkConditionCart($cart, $this->priceRule));
    }
    
    public function testPriceRuleCondTotalPerCustomer()
    {
        $total = new TotalPerCustomer();
        $total->setTotal(1);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($total->checkConditionCart($cart, $this->priceRule));

        //@todo: create order an test pricerule again with assertFalse result
    }

    public function testPriceRuleCondCountry()
    {
        $country = new Countries();
        $country->setCountries([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($country->checkConditionCart($cart, $this->priceRule));

        $country->setCountries([1]);

        $this->assertFalse($country->checkConditionCart($cart, $this->priceRule));
    }

    public function testPriceRuleCondZone()
    {
        $zone = new Zones();
        $zone->setZones([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($zone->checkConditionCart($cart, $this->priceRule));

        $zone->setZones([1]);

        $this->assertFalse($zone->checkConditionCart($cart, $this->priceRule));
    }

    public function testPriceRuleCondCategory()
    {
    }

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

    public function testPriceRuleActionGift()
    {
        $gift = new Gift();
        $gift->setGift(Data::$product1);

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);

        $this->priceRule->setActions(array($gift));

        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $this->assertEquals(150 + 24, $cart->getTotal());
        $this->assertEquals(Data::$product1->getPrice(), $cart->getDiscount());
    }

    public function testPriceRuleActionFreeShipping()
    {
        $freeShipping = new FreeShipping();

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);

        $this->priceRule->setActions(array($freeShipping));

        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $this->assertEquals(0, $cart->getShipping());
    }

    public function testPriceRuleActionDiscountAmount()
    {
        $discount = new DiscountAmount();
        $discount->setAmount(10);

        $this->priceRule->setActions(array($discount));

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);
        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $this->assertEquals($cart2->getTotal() - 10, $cart->getTotal());
    }

    public function testPriceRuleActionDiscountPercent()
    {
        $discount = new DiscountPercent();
        $discount->setPercent(10);

        $this->priceRule->setActions(array($discount));

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);
        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $this->assertEquals($cart2->getSubtotal() * 0.1, $this->priceRule->getDiscount($cart));
    }
}
