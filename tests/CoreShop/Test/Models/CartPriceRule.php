<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class CartPriceRule extends Base
{
    /**
     * @var CartPriceRuleInterface
     */
    protected $priceRule;

    /**
     * Setup Test
     */
    public function setUp()
    {
        parent::setUp();

        /*$priceRule = \CoreShop\Model\Cart\PriceRule::create();
        $priceRule->setName("test-rule");
        $priceRule->setActive(true);
        $priceRule->setHighlight(false);
        $priceRule->setCode("");
        $priceRule->setLabel("test-rule");
        $priceRule->setDescription("");

        $this->priceRule = $priceRule;*/
    }

    /**
     * Test Price Rule Condition Customer
     */
    public function testPriceRuleCondCustomer()
    {
        $this->printTodoTestName();
        //TODO

        /*$customerConditon = new Customers();
        $customerConditon->setCustomers([Data::$customer1->getId()]);

        $this->priceRule->setConditions([
            $customerConditon
        ]);

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customerConditon->checkConditionCart($cart, $this->priceRule));*/
    }

    /**
     * Test Price Rule Condition Time Span
     */
    public function testPriceRuleCondTimeSpan()
    {
        $this->printTodoTestName();
        //TODO

        /*$today = strtotime('12:00:00');
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

        $this->assertFalse($timeSpan->checkConditionCart($cart, $this->priceRule));*/
    }

    /**
     * Test Price Rule Condition Amount
     */
    public function testPriceRuleCondAmount()
    {
        $this->printTodoTestName();
        //TODO

        /*$amount = new Amount();
        $amount->setMinAmount(2);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($amount->checkConditionCart($cart, $this->priceRule));

        $amount->setMinAmount(10000);

        $this->assertFalse($amount->checkConditionCart($cart, $this->priceRule));*/
    }

    /**
     * Test Price Rule Condition Total Per Customer
     */
    public function testPriceRuleCondTotalPerCustomer()
    {
        $this->printTodoTestName();
        //TODO
        /*$total = new TotalPerCustomer();
        $total->setTotal(1);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($total->checkConditionCart($cart, $this->priceRule));*/
    }

    /**
     * Test Price Rule Condition Country
     */
    public function testPriceRuleCondCountry()
    {
        $this->printTodoTestName();
        //TODO

        /*$country = new Countries();
        $country->setCountries([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($country->checkConditionCart($cart, $this->priceRule));

        $country->setCountries([1]);

        $this->assertFalse($country->checkConditionCart($cart, $this->priceRule));*/
    }

    /**
     * Test Price Rule Condition Zone
     */
    public function testPriceRuleCondZone()
    {
        $this->printTodoTestName();
        //TODO

        /*$zone = new Zones();
        $zone->setZones([2]);

        $cart = Data::createCartWithProducts();

        $this->assertTrue($zone->checkConditionCart($cart, $this->priceRule));

        $zone->setZones([1]);

        $this->assertFalse($zone->checkConditionCart($cart, $this->priceRule));*/
    }

    /**
     * Test Price Rule Condition Category
     */
    public function testPriceRuleCondCategory()
    {
        $this->printTodoTestName();
        //TODO
    }

    /**
     * Test Price Rule Condition Customer Group
     */
    public function testPriceRuleCondCustomerGroup()
    {
        //TODO
        $this->printTodoTestName();

        /*$customer = new CustomerGroups();
        $customer->setCustomerGroups([Data::$customerGroup1->getId()]);

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customer->checkConditionCart($cart, $this->priceRule));

        $customer->setCustomerGroups([Data::$customerGroup2->getId()]);

        $this->assertFalse($customer->checkConditionCart($cart, $this->priceRule));*/
    }

    /**
     * Test Price Rule Action Gift
     */
    public function testPriceRuleActionGift()
    {
        $this->printTodoTestName();
        //TODO

        /*$gift = new Gift();
        $gift->setGift(Data::$product1->getId());

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);

        $this->priceRule->setActions([$gift]);

        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $this->assertEquals(150 + 24, $cart->getTotal());
        $this->assertEquals(Data::$product1->getPrice(), $cart->getDiscount());*/
    }

    /**
     * Test Price Rule Action Free Shipping
     */
    public function testPriceRuleActionFreeShipping()
    {
        $this->printTodoTestName();
        //TODO
        /*
        $freeShipping = new FreeShipping();

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);

        $this->priceRule->setActions([$freeShipping]);

        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $this->assertEquals(0, $cart->getShipping());*/
    }

    /**
     * Test Price Rule Action Discount Amount
     */
    public function testPriceRuleActionDiscountAmount()
    {
        $this->printTodoTestName();
        //TODO

        /*$discount = new DiscountAmount();
        $discount->setAmount(10);

        $this->priceRule->setActions([$discount]);

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);
        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $this->assertEquals($cart2->getTotal() - 10, $cart->getTotal());*/
    }

    /**
     * Test Price Rule Action Percent
     */
    public function testPriceRuleActionDiscountPercent()
    {
        $this->printTodoTestName();
        //TODO

        /*$discount = new DiscountPercent();
        $discount->setPercent(10);

        $this->priceRule->setActions([$discount]);

        $cart = Data::createCart();
        $cart->addItem(Data::$product2);
        $cart->addPriceRule($this->priceRule, $this->priceRule->getCode());

        $cart2 = Data::createCart();
        $cart2->addItem(Data::$product2);

        $this->assertEquals($cart2->getSubtotal() * 0.1, $this->priceRule->getDiscount($cart));*/
    }
}
