<?php

namespace CoreShop\Test\Models\Product;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;
use Pimcore\Cache;

class PriceRule extends Base
{
    /**
     * @var ProductPriceRuleInterface
     */
    protected $priceRule;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();

       /* $priceRule = \CoreShop\Model\Product\PriceRule::create();
        $priceRule->setName("test-rule");
        $priceRule->setActive(true);
        $priceRule->setDescription("");

        $this->priceRule = $priceRule;
        $this->product = Data::$product1;*/
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

        $this->assertTrue($customerConditon->checkConditionProduct($this->product, $this->priceRule));*/
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

        $this->assertTrue($timeSpan->checkConditionProduct($this->product, $this->priceRule));

        $timeSpan->setDateFrom($yesterday->getTimestamp() * 1000);
        $timeSpan->setDateTo($yesterday->getTimestamp() * 1000);

        $this->assertFalse($timeSpan->checkConditionProduct($this->product, $this->priceRule));*/
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

        $this->assertTrue($country->checkConditionProduct($this->product, $this->priceRule));

        $country->setCountries([1]);

        $this->assertFalse($country->checkConditionProduct($this->product, $this->priceRule));*/
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

        $this->assertTrue($zone->checkConditionProduct($this->product, $this->priceRule));

        $zone->setZones([1]);

        $this->assertFalse($zone->checkConditionProduct($this->product, $this->priceRule));*/
    }

    /**
     * Test Price Rule Condition Customer Group
     */
    public function testPriceRuleCondCustomerGroup()
    {
        $this->printTodoTestName();
        //TODO
        /*
        $customer = new CustomerGroups();
        $customer->setCustomerGroups([Data::$customerGroup1->getId()]);

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customer->checkConditionProduct($this->product, $this->priceRule));

        $customer->setCustomerGroups([Data::$customerGroup2->getId()]);

        $this->assertFalse($customer->checkConditionProduct($this->product, $this->priceRule));*/
    }

    /**
     * Test Price Rule Action Discount Amount
     */
    public function testPriceRuleActionDiscountAmount()
    {
        $this->printTodoTestName();
        //TODO
        /*
        $discount = new DiscountAmount();
        $discount->setAmount(10);

        $this->priceRule->setActions([$discount]);
        $this->priceRule->save();

        $retailPriceWithoutTax = $this->product->getRetailPrice() - 10;

        $taxCalculator = $this->product->getTaxCalculator();

        if ($taxCalculator) {
            $retailPriceWithoutTax = $taxCalculator->addTaxes($retailPriceWithoutTax);
        }

        $this->assertEquals($this->product->getPrice(), $retailPriceWithoutTax);

        $this->priceRule->setActions([]);
        $this->priceRule->save();*/
    }

    /**
     * Test Price Rule Action Discount Percent
     */
    public function testPriceRuleActionDiscountPercent()
    {
        $this->printTodoTestName();
        //TODO
        /*
        $discount = new DiscountPercent();
        $discount->setPercent(10);

        $this->priceRule->setActions([$discount]);
        $this->priceRule->save();

        Cache::clearAll();

        $retailPriceWithoutTax = $this->product->getRetailPrice() - ($this->product->getRetailPrice() * 0.1);

        $taxCalculator = $this->product->getTaxCalculator();

        if ($taxCalculator) {
            $retailPriceWithoutTax = $taxCalculator->addTaxes($retailPriceWithoutTax);
        }

        $this->assertEquals($this->product->getPrice(), $retailPriceWithoutTax);

        $this->priceRule->setActions([]);
        $this->priceRule->save();*/
    }

    /**
     * Test Price Rule Action New Price
     */
    public function testPriceRuleActionNewPrice()
    {
        $this->printTodoTestName();
        //TODO
        /*
        $newPrice = new NewPrice();
        $newPrice->setNewPrice(150);

        $this->priceRule->setActions([$newPrice]);
        $this->priceRule->save();

        Cache::clearAll();

        $retailPriceWithoutTax = 150;

        $taxCalculator = $this->product->getTaxCalculator();

        if ($taxCalculator) {
            $retailPriceWithoutTax = $taxCalculator->addTaxes($retailPriceWithoutTax);
        }

        $this->assertEquals($this->product->getPrice(), $retailPriceWithoutTax);

        $this->priceRule->setActions([]);
        $this->priceRule->save();*/
    }
}
