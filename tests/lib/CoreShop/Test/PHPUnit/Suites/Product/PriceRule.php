<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\PHPUnit\Suites\Product;

use Carbon\Carbon;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CategoriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CountriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CurrenciesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomerGroupsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ProductsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\StoresConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ZonesConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRuleActionType;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductPriceRuleConditionType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Action\DiscountAmountConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Action\DiscountPercentConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Action\PriceConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\ProductPriceNestedConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;

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
     * Setup.
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
     * {@inheritdoc}
     */
    protected function getActionFormRegistryName()
    {
        return 'coreshop.form_registry.product_price_rule.actions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionProcessorName()
    {
        return 'coreshop.product_price_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return ProductPriceRuleActionType::class;
    }

    /**
     * @return TaxedProductPriceCalculatorInterface
     */
    protected function getTaxedPriceCalculator()
    {
        return $this->get('coreshop.product.taxed_price_calculator');
    }

    /**
     * @return ProductPriceCalculatorInterface
     */
    protected function getPriceCalculator()
    {
        return $this->get('coreshop.product.price_calculator.product_price_rules');
    }

    /**
     * @return ProductPriceRuleInterface
     */
    protected function createRule()
    {
        /**
         * @var ProductPriceRuleInterface
         */
        $priceRule = $this->getFactory('product_price_rule')->createNew();
        $priceRule->setName('test-rule');
        $priceRule->setActive(true);
        $priceRule->setDescription('');

        return $priceRule;
    }

    /**
     * Test Price Rule Condition Customer.
     */
    public function testPriceRuleConditionCustomer()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $condition = $this->createConditionWithForm('customers', [
            'customers' => [Data::$customer1->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Time Span.
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
            'dateTo' => $tomorrow->getTimestamp() * 1000,
        ]);

        $this->assertRuleCondition($this->product, $condition);

        $condition = $this->createConditionWithForm('timespan', [
            'dateFrom' => $yesterday->getTimestamp() * 1000,
            'dateTo' => $yesterday->getTimestamp() * 1000,
        ]);

        $this->assertRuleCondition($this->product, $condition, [], false);
    }

    /**
     * Test Price Rule Condition Country.
     */
    public function testPriceRuleConditionCountry()
    {
        $this->printTestName();
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $condition = $this->createConditionWithForm('countries', [
            'countries' => [Data::$store->getBaseCountry()->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Zone.
     */
    public function testPriceRuleConditionZone()
    {
        $this->printTestName();
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $condition = $this->createConditionWithForm('zones', [
            'zones' => [Data::$store->getBaseCountry()->getZone()->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Customer Group.
     */
    public function testPriceRuleConditionCustomerGroup()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $condition = $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [Data::$customerGroup1->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Products.
     */
    public function testPriceRuleConditionProducts()
    {
        $this->printTestName();
        $this->assertConditionForm(ProductsConfigurationType::class, 'products');

        $condition = $this->createConditionWithForm('products', [
            'products' => [$this->product->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);

        $condition = $this->createConditionWithForm('products', [
            'products' => [Data::$product2->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition, [], false);
    }

    /**
     * Test Price Rule Condition Categories.
     */
    public function testPriceRuleConditionCategories()
    {
        $this->printTestName();
        $this->assertConditionForm(CategoriesConfigurationType::class, 'categories');

        $condition = $this->createConditionWithForm('categories', [
            'categories' => [Data::$category1->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);

        $condition = $this->createConditionWithForm('categories', [
            'categories' => [Data::$category2->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition, [], false);
    }

    /**
     * Test Price Rule Condition Stores.
     */
    public function testPriceRuleConditionStores()
    {
        $this->printTestName();
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $condition = $this->createConditionWithForm('stores', [
            'stores' => [Data::$store->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Currencies.
     */
    public function testPriceRuleConditionCurrencies()
    {
        $this->printTestName();
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $condition = $this->createConditionWithForm('currencies', [
            'currencies' => [Data::$store->getCurrency()->getId()],
        ]);

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Condition Nested.
     */
    public function testPriceRuleConditionNested()
    {
        $this->printTestName();
        $this->assertConditionForm(ProductPriceNestedConfigurationType::class, 'nested');

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

        $this->assertRuleCondition($this->product, $condition);
    }

    /**
     * Test Price Rule Action Discount Amount.
     */
    public function testPriceRuleActionDiscountAmount()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $action = $this->createActionWithForm('discountAmount', [
            'amount' => 5,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $discount = $this->getPriceCalculator()->getDiscount($this->product, $this->getContext(), $this->product->getStorePrice(Data::$store));

        $this->assertEquals(500, $discount);
        $this->assertEquals(1000, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext(), false));
        $this->assertEquals(1200, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext()));

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Discount Amount.
     */
    public function testPriceRuleActionDiscountAmountWithOddAmount()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $action = $this->createActionWithForm('discountAmount', [
            'amount' => 5.12,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $discount = $this->getPriceCalculator()->getDiscount($this->product, $this->getContext(), $this->product->getStorePrice(Data::$store));

        $this->assertEquals(512, $discount);
        $this->assertEquals(988, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext(), false));
        $this->assertEquals(1186, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext()));

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Discount Percent.
     */
    public function testPriceRuleActionDiscountPercent()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountPercentConfigurationType::class, 'discountPercent');

        $action = $this->createActionWithForm('discountPercent', [
            'percent' => 10,
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $discount = $this->getPriceCalculator()->getDiscount($this->product, $this->getContext(), $this->product->getStorePrice(Data::$store));

        $this->assertEquals(150, $discount);
        $this->assertEquals(1350, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext(), false));
        $this->assertEquals(1620, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext()));

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action New Price.
     */
    public function testPriceRuleActionNewPrice()
    {
        $this->printTestName();
        $this->assertActionForm(PriceConfigurationType::class, 'price');

        $action = $this->createActionWithForm('price', [
            'price' => 1,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $discount = $this->getPriceCalculator()->getDiscount($this->product, $this->getContext(), $this->product->getStorePrice(Data::$store));

        $this->assertEquals(0, $discount);
        $this->assertEquals(100, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext(), false));
        $this->assertEquals(120, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext()));
        $this->assertEquals(100, $this->getTaxedPriceCalculator()->getRetailPrice($this->product, $this->getContext(), false));
        $this->assertEquals(120, $this->getTaxedPriceCalculator()->getRetailPrice($this->product, $this->getContext()));

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Discount Price.
     */
    public function testPriceRuleActionDiscountPrice()
    {
        $this->printTestName();
        $this->assertActionForm(PriceConfigurationType::class, 'discountPrice');

        $action = $this->createActionWithForm('discountPrice', [
            'price' => 1,
            'currency' => Data::$store->getCurrency()->getId(),
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $discount = $this->getPriceCalculator()->getDiscount($this->product, $this->getContext(), $this->product->getStorePrice(Data::$store));

        $this->assertEquals(0, $discount);
        $this->assertEquals(1500, $this->getTaxedPriceCalculator()->getRetailPrice($this->product, $this->getContext(), false));
        $this->assertEquals(1800, $this->getTaxedPriceCalculator()->getRetailPrice($this->product, $this->getContext()));
        $this->assertEquals(100, $this->getTaxedPriceCalculator()->getDiscountPrice($this->product, $this->getContext(), false));
        $this->assertEquals(120, $this->getTaxedPriceCalculator()->getDiscountPrice($this->product, $this->getContext()));
        $this->assertEquals(100, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext(), false));
        $this->assertEquals(120, $this->getTaxedPriceCalculator()->getPrice($this->product, $this->getContext()));

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }
}
