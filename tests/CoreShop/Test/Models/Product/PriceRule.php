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

namespace CoreShop\Test\Models\Product;

use CoreShop\Model\PriceRule\Action\DiscountAmount;
use CoreShop\Model\PriceRule\Action\DiscountPercent;
use CoreShop\Model\PriceRule\Action\NewPrice;
use CoreShop\Model\PriceRule\Condition\Customer;
use CoreShop\Model\PriceRule\Condition\TimeSpan;
use CoreShop\Model\PriceRule\Condition\Amount;
use CoreShop\Model\PriceRule\Condition\Category as ConditionCategory;
use CoreShop\Model\PriceRule\Condition\Country as ConditionCountry;
use CoreShop\Model\PriceRule\Condition\CustomerGroup as ConditionCustomerGroup;
use CoreShop\Model\PriceRule\Condition\Product as ConditionProduct;
use CoreShop\Model\PriceRule\Condition\TotalPerCustomer;
use CoreShop\Model\PriceRule\Condition\Zone as ConditionZone;
use CoreShop\Test\Base;
use CoreShop\Test\Data;
use CoreShop\Tool;
use Pimcore\Cache;

class PriceRule extends Base
{
    /**
     * @var \CoreShop\Model\Product\PriceRule
     */
    protected $priceRule;

    /**
     * @var \CoreShop\Model\Product
     */
    protected $product;

    public function setUp()
    {
        parent::setUp();

        $priceRule = new \CoreShop\Model\Product\PriceRule();
        $priceRule->setName("test-rule");
        $priceRule->setActive(true);
        $priceRule->setDescription("");

        $this->priceRule = $priceRule;
        $this->product = Data::$product1;
    }

    public function testPriceRuleCondCustomer()
    {
        $customerConditon = new Customer();
        $customerConditon->setCustomer(Data::$customer1->getId());

        $this->priceRule->setConditions(array(
            $customerConditon
        ));

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customerConditon->checkConditionProduct($this->product, $this->priceRule));
    }

    public function testPriceRuleCondTimeSpan()
    {
        $today              = strtotime('12:00:00');
        $yesterday          = strtotime('-1 day', $today);
        $tomorrow          = strtotime('1 day', $today);

        $timeSpan = new TimeSpan();
        $timeSpan->setDateFrom(new \Zend_Date($yesterday));
        $timeSpan->setDateTo(new \Zend_Date($tomorrow));

        $this->assertTrue($timeSpan->checkConditionProduct($this->product, $this->priceRule));

        $timeSpan->setDateFrom($yesterday);
        $timeSpan->setDateTo($yesterday);

        $this->assertFalse($timeSpan->checkConditionProduct($this->product, $this->priceRule));
    }

    public function testPriceRuleCondCountry()
    {
        $country = new ConditionCountry();
        $country->setCountry(\CoreShop\Model\Country::getById(2));

        $this->assertTrue($country->checkConditionProduct($this->product, $this->priceRule));

        $country->setCountry(\CoreShop\Model\Country::getById(1));

        $this->assertFalse($country->checkConditionProduct($this->product, $this->priceRule));
    }

    public function testPriceRuleCondZone()
    {
        $zone = new ConditionZone();
        $zone->setZone(\CoreShop\Model\Zone::getById(1));

        $this->assertTrue($zone->checkConditionProduct($this->product, $this->priceRule));

        $zone->setZone(\CoreShop\Model\Zone::getById(2));

        $this->assertFalse($zone->checkConditionProduct($this->product, $this->priceRule));
    }

    public function testPriceRuleCondCustomerGroup()
    {
        $customer = new ConditionCustomerGroup();
        $customer->setCustomerGroup(Data::$customerGroup1);

        $cart = Data::createCartWithProducts();
        $cart->setUser(Data::$customer1);

        $this->assertTrue($customer->checkConditionProduct($this->product, $this->priceRule));

        $customer->setCustomerGroup(Data::$customerGroup2);

        $this->assertFalse($customer->checkConditionProduct($this->product, $this->priceRule));
    }

    public function testPriceRuleActionDiscountAmount()
    {
        $discount = new DiscountAmount();
        $discount->setAmount(10);

        $this->priceRule->setActions(array($discount));
        $this->priceRule->save();

        $retailPriceWithoutTax = $this->product->getRetailPrice() - 10;

        $taxCalculator = $this->product->getTaxCalculator();

        if ($taxCalculator) {
            $retailPriceWithoutTax = $taxCalculator->addTaxes($retailPriceWithoutTax);
        }

        $this->assertEquals($this->product->getPrice(), $retailPriceWithoutTax);

        $this->priceRule->setActions(array());
        $this->priceRule->save();
    }

    public function testPriceRuleActionDiscountPercent()
    {
        $discount = new DiscountPercent();
        $discount->setPercent(10);

        $this->priceRule->setActions(array($discount));
        $this->priceRule->save();

        Cache::clearAll();

        $retailPriceWithoutTax = $this->product->getRetailPrice() - ($this->product->getRetailPrice() * 0.1);

        $taxCalculator = $this->product->getTaxCalculator();

        if ($taxCalculator) {
            $retailPriceWithoutTax = $taxCalculator->addTaxes($retailPriceWithoutTax);
        }

        $this->assertEquals($this->product->getPrice(), $retailPriceWithoutTax);

        $this->priceRule->setActions(array());
        $this->priceRule->save();
    }

    public function testPriceRuleActionNewPrice()
    {
        $newPrice = new NewPrice();
        $newPrice->setNewPrice(150);

        $this->priceRule->setActions(array($newPrice));
        $this->priceRule->save();

        Cache::clearAll();

        $retailPriceWithoutTax = 150;

        $taxCalculator = $this->product->getTaxCalculator();

        if ($taxCalculator) {
            $retailPriceWithoutTax = $taxCalculator->addTaxes($retailPriceWithoutTax);
        }

        $this->assertEquals($this->product->getPrice(), $retailPriceWithoutTax);

        $this->priceRule->setActions(array());
        $this->priceRule->save();
    }
}
