<?php

namespace CoreShop\Test\Models;

use Carbon\Carbon;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CountriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CurrenciesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomerGroupsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\StoresConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ZonesConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleActionType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleConditionType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\DiscountAmountConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\DiscountPercentConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Component\Order\Cart\Calculator\CartDiscountCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;

class CartPriceRule extends RuleTest
{
    /**
     * @var CartPriceRuleInterface
     */
    protected $priceRule;

    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();

        $this->cart = Data::createCartWithProducts();
        $this->cart->setCustomer(Data::$customer1);
        $this->cart->setShippingAddress(Data::$customer1->getAddresses()[0]);
        $this->cart->setInvoiceAddress(Data::$customer1->getAddresses()[0]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistryName()
    {
        return 'coreshop.form_registry.cart_price_rule.conditions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionValidatorName()
    {
        return 'coreshop.cart_price_rule.rule_validation.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return CartPriceRuleConditionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormRegistryName()
    {
        return 'coreshop.form_registry.cart_price_rule.actions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionProcessorName()
    {
        return 'coreshop.cart_price_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return CartPriceRuleActionType::class;
    }

    /**
     * @return CartDiscountCalculatorInterface
     */
    protected function getPriceCalculator() {
        return $this->get('coreshop.cart.discount_calculator.price_rules');
    }

    /**
     * @return CartPriceRuleInterface
     */
    protected function createRule() {
        /**
         * @var $priceRule CartPriceRuleInterface
         */
        $priceRule = $this->getFactory('cart_price_rule')->createNew();
        $priceRule->setName('test-rule');

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

        $this->assertRuleCondition($this->cart, $condition);
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

        $this->assertRuleCondition($this->cart, $condition);

        $condition = $this->createConditionWithForm('timespan', [
            'dateFrom' => $yesterday->getTimestamp() * 1000,
            'dateTo' => $yesterday->getTimestamp() * 1000
        ]);

        $this->assertRuleCondition($this->cart, $condition, false);
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

        $this->assertRuleCondition($this->cart, $condition);
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

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Customer Group
     */
    public function testPriceRuleConditionCustomerGroup()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $condition = $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [Data::$customerGroup1->getId()]
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Stores
     */
    public function testPriceRuleConditionStores()
    {
        $this->printTestName();
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $condition = $this->createConditionWithForm('stores', [
            'stores' => [Data::$store->getId()]
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Currencies
     */
    public function testPriceRuleConditionCurrencies()
    {
        $this->printTestName();
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $condition = $this->createConditionWithForm('currencies', [
            'currencies' => [Data::$store->getBaseCurrency()->getId()]
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }


    /**
     * Test Price Rule Action Discount Amount
     */
    public function testPriceRuleActionDiscountAmount()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $action = $this->createActionWithForm('discountAmount', [
            'amount' => 5
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();


    }

    /**
     * Test Price Rule Action Discount Percent
     */
    public function testPriceRuleActionDiscountPercent()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountPercentConfigurationType::class, 'discountPercent');

        $action = $this->createActionWithForm('discountPercent', [
            'percent' => 10
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);
    }

}
