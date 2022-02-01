<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CategoriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CountriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CurrenciesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomerGroupsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ProductsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\StoresConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ZonesConfigurationType;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\Shipping\Rule\Action\AdditionAmountActionConfigurationType;
use CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Action\AdditionPercentActionConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Shipping\Rule\Action\DiscountAmountActionConfigurationType;
use CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Action\DiscountPercentActionConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Shipping\Rule\Action\PriceActionConfigurationType;
use CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Condition\NestedConfigurationType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleActionType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleConditionType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;

class ShippingRule extends RuleTest
{
    /**
     * @var ShippingRuleInterface
     */
    protected $ShippingRule;

    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * @var AddressInterface
     */
    protected $address;

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();

        $this->cart = Data::createCartWithProducts();
        $this->cart->setCustomer(Data::$customer1);

        $this->address = Data::$customer1->getAddresses()[0];
    }

    /**
     * @return CarrierInterface
     */
    private function createCarrier()
    {
        /**
         * @var $carrier CarrierInterface
         */
        $carrier = $this->getFactory('carrier')->createNew();
        $carrier->setIdentifier('test');
        $carrier->setTaxRule(Data::$taxRuleGroup);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        return $carrier;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistryName()
    {
        return 'coreshop.form_registry.shipping_rule.conditions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionValidatorName()
    {
        return 'coreshop.shipping_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return ShippingRuleConditionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormRegistryName()
    {
        return 'coreshop.form_registry.shipping_rule.actions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionProcessorName()
    {
        return 'coreshop.shipping.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return ShippingRuleActionType::class;
    }

    /**
     * @return CarrierPriceCalculatorInterface
     */
    protected function getPriceCalculator()
    {
        return $this->get('coreshop.carrier.price_calculator.taxed');
    }

    /**
     * @param bool $active
     * @return ShippingRuleInterface
     */
    protected function createRule($active = true)
    {
        /**
         * @var ShippingRuleInterface
         */
        $shippingRule = $this->getFactory('shipping_rule')->createNew();
        $shippingRule->setName('test-rule');
        $shippingRule->setActive($active);

        return $shippingRule;
    }

    /**
     * @param ShippingRuleInterface $rule
     *
     * @return ShippingRuleGroupInterface
     */
    protected function createShippingRuleGroup(ShippingRuleInterface $rule)
    {
        /**
         * @var ShippingRuleGroupInterface
         */
        $shippingRuleGroup = $this->getFactory('shipping_rule_group')->createNew();
        $shippingRuleGroup->setPriority(1);
        $shippingRuleGroup->setShippingRule($rule);

        return $shippingRuleGroup;
    }

    /**
     * Test Price Rule Condition Customer.
     */
    public function testShippingRuleConditionCustomer()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $condition = $this->createConditionWithForm('customers', [
            'customers' => [Data::$customer1->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * @param $subject
     * @param ConditionInterface $condition
     * @param array $params
     * @param bool $trueOrFalse
     */
    protected function assertRuleCondition($subject, ConditionInterface $condition, $params = [], $trueOrFalse = true)
    {
        $rule = $this->createRule();
        $rule->addCondition($condition);

        $carrier = $this->createCarrier();
        $group = $this->createShippingRuleGroup($rule);
        $carrier->addShippingRule($group);

        $this->getEntityManager()->persist($group);
        $this->getEntityManager()->flush();

        $this->assertPriceRuleCondition($carrier, $rule, ['shippable' => $this->cart, 'address' => $this->address], $trueOrFalse);
    }

    /**
     * Test Price Rule Condition Country.
     */
    public function testShippingRuleConditionCountry()
    {
        $this->printTestName();
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $condition = $this->createConditionWithForm('countries', [
            'countries' => [Data::$store->getBaseCountry()->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Zone.
     */
    public function testShippingRuleConditionZone()
    {
        $this->printTestName();
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $condition = $this->createConditionWithForm('zones', [
            'zones' => [Data::$store->getBaseCountry()->getZone()->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Customer Group.
     */
    public function testShippingRuleConditionCustomerGroup()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $condition = $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [Data::$customerGroup1->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Products.
     */
    public function testShippingRuleConditionProducts()
    {
        $this->printTestName();
        $this->assertConditionForm(ProductsConfigurationType::class, 'products');

        $condition = $this->createConditionWithForm('products', [
            'products' => [Data::$product1->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);

        $condition = $this->createConditionWithForm('products', [
            'products' => [Data::$product2->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Categories.
     */
    public function testShippingRuleConditionCategories()
    {
        $this->printTestName();
        $this->assertConditionForm(CategoriesConfigurationType::class, 'categories');

        $condition = $this->createConditionWithForm('categories', [
            'categories' => [Data::$category1->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);

        $condition = $this->createConditionWithForm('categories', [
            'categories' => [Data::$category2->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Stores.
     */
    public function testShippingRuleConditionStores()
    {
        $this->printTestName();
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $condition = $this->createConditionWithForm('stores', [
            'stores' => [Data::$store->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Currencies.
     */
    public function testShippingRuleConditionCurrencies()
    {
        $this->printTestName();
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $condition = $this->createConditionWithForm('currencies', [
            'currencies' => [Data::$store->getCurrency()->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Nested.
     */
    public function testShippingRuleConditionNested()
    {
        $this->printTestName();
        $this->assertConditionForm(NestedConfigurationType::class, 'nested');

        $categoriesCondition = $this->createConditionWithForm('categories', [
            'categories' => [Data::$category1->getId()],
        ]);

        $currencyCondition = $this->createConditionWithForm('currencies', [
            'currencies' => [Data::$store->getCurrency()->getId()],
        ]);

        $condition = $this->createConditionWithForm('nested', [
            'nested' => [$categoriesCondition, $currencyCondition],
            'operator' => 'AND',
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Action Discount Amount.
     */
    public function testShippingRuleActionDiscountAmount()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountAmountActionConfigurationType::class, 'discountAmount');

        $action1 = $this->createActionWithForm('price', [
            'price' => 100,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $action2 = $this->createActionWithForm('discountAmount', [
            'amount' => 5,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $rule = $this->createRule();
        $rule->addAction($action1);
        $rule->addAction($action2);

        $this->getEntityManager()->persist($rule);

        $group = $this->createShippingRuleGroup($rule);

        $carrier = $this->createCarrier();
        $carrier->addShippingRule($group);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        $price = $this->getPriceCalculator()->getPrice($carrier, $this->cart, $this->address, true);

        $this->assertEquals(11400, $price);

        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Discount Percent.
     */
    public function testShippingRuleActionDiscountPercent()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountPercentActionConfigurationType::class, 'discountPercent');

        $action1 = $this->createActionWithForm('price', [
            'price' => 100,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $action2 = $this->createActionWithForm('discountPercent', [
            'percent' => 10,
        ]);

        $rule = $this->createRule();
        $rule->addAction($action1);
        $rule->addAction($action2);

        $this->getEntityManager()->persist($rule);

        $group = $this->createShippingRuleGroup($rule);

        $carrier = $this->createCarrier();
        $carrier->addShippingRule($group);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        $price = $this->getPriceCalculator()->getPrice($carrier, $this->cart, $this->address, true);

        $this->assertEquals(10800, $price);

        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action New Price.
     */
    public function testShippingRuleActionPrice()
    {
        $this->printTestName();
        $this->assertActionForm(PriceActionConfigurationType::class, 'price');

        $action = $this->createActionWithForm('price', [
            'price' => 100,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);

        $group = $this->createShippingRuleGroup($rule);

        $carrier = $this->createCarrier();
        $carrier->addShippingRule($group);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        $price = $this->getPriceCalculator()->getPrice($carrier, $this->cart, $this->address, true);

        $this->assertEquals(12000, $price);

        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Addition Amount.
     */
    public function testShippingRuleActionAdditionAmount()
    {
        $this->printTestName();
        $this->assertActionForm(AdditionAmountActionConfigurationType::class, 'additionAmount');

        $action1 = $this->createActionWithForm('price', [
            'price' => 100,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $action2 = $this->createActionWithForm('additionAmount', [
            'amount' => 5,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $rule = $this->createRule();
        $rule->addAction($action1);
        $rule->addAction($action2);

        $this->getEntityManager()->persist($rule);

        $group = $this->createShippingRuleGroup($rule);

        $carrier = $this->createCarrier();
        $carrier->addShippingRule($group);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        $price = $this->getPriceCalculator()->getPrice($carrier, $this->cart, $this->address, true);

        $this->assertEquals(12600, $price);

        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Addition Percent.
     */
    public function testShippingRuleActionAdditionPercent()
    {
        $this->printTestName();
        $this->assertActionForm(AdditionPercentActionConfigurationType::class, 'additionPercent');

        $action1 = $this->createActionWithForm('price', [
            'price' => 100,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $action2 = $this->createActionWithForm('additionPercent', [
            'percent' => 10,
        ]);

        $rule = $this->createRule();
        $rule->addAction($action1);
        $rule->addAction($action2);

        $this->getEntityManager()->persist($rule);

        $group = $this->createShippingRuleGroup($rule);

        $carrier = $this->createCarrier();
        $carrier->addShippingRule($group);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        $price = $this->getPriceCalculator()->getPrice($carrier, $this->cart, $this->address, true);

        $this->assertEquals(13200, $price);

        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }
}
