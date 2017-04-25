<?php

namespace CoreShop\Test\Models\Product;

use Carbon\Carbon;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CountriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ZonesConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRuleConditionType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;
use Pimcore\Cache;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;

class PriceRule extends RuleTest
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

        $this->product = Data::$product1;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistryName()
    {
        return 'coreshop.form_registry.product_price_rule.conditions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionValidatorName()
    {
        return 'coreshop.product_price_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return ProductPriceRuleConditionType::class;
    }


    /**
     * @return ProductPriceRuleInterface
     */
    protected function createRule() {
        /**
         * @var $priceRule ProductPriceRuleInterface
         */
        $priceRule = $this->getFactory('product_price_rule')->createNew();
        $priceRule->setName('test-rule');
        $priceRule->setActive(true);
        $priceRule->setDescription('');

        return $priceRule;
    }

    /**
     * Test Price Rule Condition Customer
     */
    public function testPriceRuleConditionCustomer()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $condition = $this->createConditionWithForm('customers', [
            'customers' => [Data::$customer1->getId()]
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Time Span
     */
    public function testPriceRuleConditionTimeSpan()
    {
        $this->printTestName();

        $this->assertConditionForm(TimespanConfigurationType::class, 'timespan');

        $today = strtotime('12:00:00');
        $yesterday = strtotime('-1 day', $today);
        $tomorrow = strtotime('1 day', $today);

        $yesterday = Carbon::createFromTimestamp($yesterday);
        $tomorrow = Carbon::createFromTimestamp($tomorrow);

        $condition = $this->createConditionWithForm('timespan', [
            'dateFrom' => $yesterday->getTimestamp() * 1000,
            'dateTo' => $tomorrow->getTimestamp() * 1000
        ]);

        $this->assertRuleCondition($this->product, $condition);

        $condition = $this->createConditionWithForm('timespan', [
            'dateFrom' => $yesterday->getTimestamp() * 1000,
            'dateTo' => $yesterday->getTimestamp() * 1000
        ]);

        $this->assertRuleCondition($this->product, $condition, false);
    }

    /**
     * Test Price Rule Condition Country
     */
    public function testPriceRuleConditionCountry()
    {
        $this->printTestName();
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $condition = $this->createConditionWithForm('countries', [
            'countries' => [Data::$store->getBaseCountry()->getId()]
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Zone
     */
    public function testPriceRuleConditionZone()
    {
        $this->printTestName();
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $condition = $this->createConditionWithForm('zones', [
            'zones' => [Data::$store->getBaseCountry()->getZone()->getId()]
        ]);

        $this->assertRuleCondition($this->product, $condition);
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
