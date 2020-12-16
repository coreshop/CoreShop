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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\ProductPriceRule\Condition\QuantityConfigurationType;
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
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;

final class ProductPriceRuleContext implements Context
{
    use ConditionFormTrait;
    use ActionFormTrait;

    private $sharedStorage;
    private $objectManager;
    private $formFactory;
    private $conditionFormTypeRegistry;
    private $actionFormTypeRegistry;
    private $productPriceRuleFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $conditionFormTypeRegistry,
        FormTypeRegistryInterface $actionFormTypeRegistry,
        FactoryInterface $productPriceRuleFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->formFactory = $formFactory;
        $this->conditionFormTypeRegistry = $conditionFormTypeRegistry;
        $this->actionFormTypeRegistry = $actionFormTypeRegistry;
        $this->productPriceRuleFactory = $productPriceRuleFactory;
    }

    /**
     * @Given /^adding a product price rule named "([^"]+)"$/
     */
    public function addingAProductPriceRule($ruleName)
    {
        /**
         * @var ProductPriceRuleInterface $rule
         */
        $rule = $this->productPriceRuleFactory->createNew();
        $rule->setName($ruleName);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();

        $this->sharedStorage->set('product-price-rule', $rule);
    }

    /**
     * @Given /^the (price rule "[^"]+") is active$/
     * @Given /^the (price rule) is active$/
     */
    public function theProductPriceRuleIsActive(ProductPriceRuleInterface $rule)
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (price rule "[^"]+") is inactive$/
     * @Given /^the (price rule) is inactive$/
     */
    public function theProductPriceRuleIsInActive(ProductPriceRuleInterface $rule)
    {
        $rule->setActive(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (price rule "[^"]+") is stop propagation$/
     * @Given /^the (price rule) is stop propagation$/
     */
    public function theProductPriceRuleIsStopPropagation(ProductPriceRuleInterface $rule)
    {
        $rule->setStopPropagation(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (price rule "[^"]+") is not stop propagation$/
     * @Given /^the (price rule) is not stop propagation$/
     */
    public function theProductPriceRuleIsNotStopPropagation(ProductPriceRuleInterface $rule)
    {
        $rule->setStopPropagation(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (price rule "[^"]+") has priority "([\d]+)"$/
     * @Given /^the (price rule) has priority "([\d]+)"$/
     */
    public function theProductPriceRuleHasPriority(ProductPriceRuleInterface $rule, int $priority)
    {
        $rule->setPriority($priority);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition countries with (country "[^"]+")$/
     * @Given /^the (price rule) has a condition countries with (country "[^"]+")$/
     */
    public function theProductPriceRuleHasACountriesCondition(
        ProductPriceRuleInterface $rule,
        CountryInterface $country
    ) {
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $this->addCondition($rule, $this->createConditionWithForm('countries', [
            'countries' => [
                $country->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition customers with (customer "[^"]+")$/
     * @Given /^the (price rule) has a condition customers with (customer "[^"]+")$/
     */
    public function theProductPriceRuleHasACustomerCondition(
        ProductPriceRuleInterface $rule,
        CustomerInterface $customer
    ) {
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $this->addCondition($rule, $this->createConditionWithForm('customers', [
            'customers' => [
                $customer->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition timespan which is valid from "([^"]+") to "([^"]+)"$/
     * @Given /^the (price rule) has a condition timespan which is valid from "([^"]+)" to "([^"]+)"$/
     */
    public function theProductPriceRuleHasATimeSpanCondition(ProductPriceRuleInterface $rule, $from, $to)
    {
        $this->assertConditionForm(TimespanConfigurationType::class, 'timespan');

        $from = new \DateTime($from);
        $to = new \DateTime($to);

        $this->addCondition($rule, $this->createConditionWithForm('timespan', [
            'dateFrom' => $from->getTimestamp() * 1000,
            'dateTo' => $to->getTimestamp() * 1000,
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition customer-groups with (customer-group "[^"]+")$/
     * @Given /^the (price rule) has a condition customer-groups with (customer-group "[^"]+")$/
     */
    public function theProductPriceRuleHasACustomerGroupCondition(
        ProductPriceRuleInterface $rule,
        CustomerGroupInterface $group
    ) {
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $this->addCondition($rule, $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [
                $group->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition stores with (store "[^"]+")$/
     * @Given /^the (price rule) has a condition stores with (store "[^"]+")$/
     */
    public function theProductPriceRuleHasAStoreCondition(ProductPriceRuleInterface $rule, StoreInterface $store)
    {
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $this->addCondition($rule, $this->createConditionWithForm('stores', [
            'stores' => [
                $store->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition zones with (zone "[^"]+")$/
     * @Given /^the (price rule) has a condition zones with (zone "[^"]+")$/
     */
    public function theProductPriceRuleHasAZoneCondition(ProductPriceRuleInterface $rule, ZoneInterface $zone)
    {
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $this->addCondition($rule, $this->createConditionWithForm('zones', [
            'zones' => [
                $zone->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition currencies with (currency "[^"]+")$/
     * @Given /^the (price rule) has a condition currencies with (currency "[^"]+")$/
     */
    public function theProductPriceRuleHasACurrencyCondition(
        ProductPriceRuleInterface $rule,
        CurrencyInterface $currency
    ) {
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $this->addCondition($rule, $this->createConditionWithForm('currencies', [
            'currencies' => [
                $currency->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition categories with (category "[^"]+")$/
     * @Given /^the (price rule) has a condition categories with (category "[^"]+")$/
     */
    public function theProductPriceRuleHasACategoriesCondition(
        ProductPriceRuleInterface $rule,
        CategoryInterface $category
    ) {
        $this->assertConditionForm(CategoriesConfigurationType::class, 'categories');

        $this->addCondition($rule, $this->createConditionWithForm('categories', [
            'categories' => [$category->getId()],
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition categories with (category "[^"]+") and it is recursive$/
     * @Given /^the (price rule) has a condition categories with (category "[^"]+") and it is recursive$/
     */
    public function theProductPriceRuleHasACategoriesConditionAndItIsRecursive(
        ProductPriceRuleInterface $rule,
        CategoryInterface $category
    ) {
        $this->assertConditionForm(CategoriesConfigurationType::class, 'categories');

        $this->addCondition($rule, $this->createConditionWithForm('categories', [
            'categories' => [$category->getId()],
            'recursive' => true,
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition products with (product "[^"]+")$/
     * @Given /^the (price rule) has a condition products with (product "[^"]+")$/
     * @Given /^the (price rule) has a condition products with (product "[^"]+") and (product "[^"]+")$/
     */
    public function theProductPriceRuleHasAProductCondition(
        ProductPriceRuleInterface $rule,
        ProductInterface $product,
        ProductInterface $product2 = null
    ) {
        $this->assertConditionForm(ProductsConfigurationType::class, 'products');

        $configuration = [
            'products' => [
                $product->getId(),
            ],
            'include_variants' => false,
        ];

        if (null !== $product2) {
            $configuration['products'][] = $product2->getId();
        }

        $this->addCondition($rule, $this->createConditionWithForm('products', $configuration));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition products with (product "[^"]+") which includes variants$/
     * @Given /^the (price rule) has a condition products with (product "[^"]+") which includes variants$/
     * @Given /^the (price rule) has a condition products with (product "[^"]+") and (product "[^"]+") which includes variants$/
     */
    public function theProductPriceRuleHasAProductConditionWhichIncludesVariants(
        ProductPriceRuleInterface $rule,
        ProductInterface $product,
        ProductInterface $product2 = null
    ) {
        $this->assertConditionForm(ProductsConfigurationType::class, 'products');

        $configuration = [
            'products' => [
                $product->getId(),
            ],
            'include_variants' => true,
        ];

        if (null !== $product2) {
            $configuration['products'][] = $product2->getId();
        }

        $this->addCondition($rule, $this->createConditionWithForm('products', $configuration));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action discount-percent with ([^"]+)% discount$/
     * @Given /^the (price rule) has a action discount-percent with ([^"]+)% discount$/
     */
    public function theProductPriceRuleHasADiscountPercentAction(ProductPriceRuleInterface $rule, $discount)
    {
        $this->assertActionForm(DiscountPercentConfigurationType::class, 'discountPercent');

        $this->addAction($rule, $this->createActionWithForm('discountPercent', [
            'percent' => (int) $discount,
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action discount with ([^"]+) in (currency "[^"]+") off$/
     * @Given /^the (price rule) has a action discount with ([^"]+) in (currency "[^"]+") off$/
     */
    public function theProductPriceRuleHasADiscountAmountAction(
        ProductPriceRuleInterface $rule,
        $amount,
        CurrencyInterface $currency
    ) {
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $this->addAction($rule, $this->createActionWithForm('discountAmount', [
            'amount' => (int) $amount,
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action discount-price of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (price rule) has a action discount-price of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductPriceRuleHasADiscountPrice(
        ProductPriceRuleInterface $rule,
        $price,
        CurrencyInterface $currency
    ) {
        $this->assertActionForm(PriceConfigurationType::class, 'discountPrice');

        $this->addAction($rule, $this->createActionWithForm('discountPrice', [
            'price' => (int) $price,
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a action price of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (price rule) has a action price of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductPriceRuleHasAPrice(ProductPriceRuleInterface $rule, $price, CurrencyInterface $currency)
    {
        $this->assertActionForm(PriceConfigurationType::class, 'price');

        $this->addAction($rule, $this->createActionWithForm('price', [
            'price' => (int) $price,
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (price rule "[^"]+") has a condition quantity with min (\d+) and max (\d+)$/
     * @Given /^the (price rule) has a condition quantity with min (\d+) and max (\d+)$/
     */
    public function theProductPriceRuleHasAQuantityCondition(
        ProductPriceRuleInterface $rule,
        int $min,
        int $max
    ) {
        $this->assertConditionForm(QuantityConfigurationType::class, 'quantity');

        $configuration = [
            'minQuantity' => $min,
            'maxQuantity' => $max,
        ];

        $this->addCondition($rule, $this->createConditionWithForm('quantity', $configuration));
    }

    /**
     * @param ProductPriceRuleInterface $rule
     * @param ConditionInterface        $condition
     */
    private function addCondition(ProductPriceRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param ProductPriceRuleInterface $rule
     * @param ActionInterface           $action
     */
    private function addAction(ProductPriceRuleInterface $rule, ActionInterface $action)
    {
        $rule->addAction($action);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistry()
    {
        return $this->conditionFormTypeRegistry;
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
    protected function getActionFormRegistry()
    {
        return $this->actionFormTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return ProductPriceRuleActionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormFactory()
    {
        return $this->formFactory;
    }
}
