<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CountriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CurrenciesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomerGroupsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\StoresConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ZonesConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleActionType;
use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleConditionType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Action\DiscountAmountConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Action\DiscountPercentConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Action\PriceConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\ProductSpecificPriceNestedConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;

final class ProductSpecificPriceRuleContext implements Context
{
    use ConditionFormTrait;
    use ActionFormTrait;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormTypeRegistryInterface
     */
    private $conditionFormTypeRegistry;

    /**
     * @var FormTypeRegistryInterface
     */
    private $actionFormTypeRegistry;

    /**
     * @var FactoryInterface
     */
    private $productSpecificPriceRuleFactory;

    /**
     * @var ProductSpecificPriceRuleRepositoryInterface
     */
    private $productSpecificPriceRuleRepository;

    /**
     * @param SharedStorageInterface                      $sharedStorage
     * @param ObjectManager                               $objectManager
     * @param FormFactoryInterface                        $formFactory
     * @param FormTypeRegistryInterface                   $conditionFormTypeRegistry
     * @param FormTypeRegistryInterface                   $actionFormTypeRegistry
     * @param FactoryInterface                            $productSpecificPriceRuleFactory
     * @param ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $conditionFormTypeRegistry,
        FormTypeRegistryInterface $actionFormTypeRegistry,
        FactoryInterface $productSpecificPriceRuleFactory,
        ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->formFactory = $formFactory;
        $this->conditionFormTypeRegistry = $conditionFormTypeRegistry;
        $this->actionFormTypeRegistry = $actionFormTypeRegistry;
        $this->productSpecificPriceRuleFactory = $productSpecificPriceRuleFactory;
        $this->productSpecificPriceRuleRepository = $productSpecificPriceRuleRepository;
    }

    /**
     * @Given /^adding a product specific price rule to (product "[^"]+") named "([^"]+)"$/
     */
    public function addingAProductSpecificPriceRuleToProduct(ProductInterface $product, $ruleName)
    {
        /**
         * @var ProductSpecificPriceRuleInterface $rule
         */
        $rule = $this->productSpecificPriceRuleFactory->createNew();
        $rule->setName($ruleName);
        $rule->setProduct($product->getId());

        $this->objectManager->persist($rule);
        $this->objectManager->flush();

        $this->sharedStorage->set('product-specific-price-rule', $rule);
    }

    /**
     * @Given /^the (specific price rule "[^"]+") is active$/
     * @Given /^the (specific price rule) is active$/
     */
    public function theProductsSpecificPriceRuleIsActive(ProductSpecificPriceRuleInterface $rule)
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (specific price rule "[^"]+") is inactive$/
     * @Given /^the (specific price rule) is inactive$/
     */
    public function theProductsSpecificPriceRuleIsInActive(ProductSpecificPriceRuleInterface $rule)
    {
        $rule->setActive(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition countries with (country "[^"]+")$/
     * @Given /^the (specific price rule) has a condition countries with (country "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasACountriesCondition(ProductSpecificPriceRuleInterface $rule, CountryInterface $country)
    {
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $this->addCondition($rule, $this->createConditionWithForm('countries', [
            'countries' => [
                $country->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition customers with (customer "[^"]+")$/
     * @Given /^the (specific price rule) has a condition customers with (customer "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasACustomerCondition(ProductSpecificPriceRuleInterface $rule, CustomerInterface $customer)
    {
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $this->addCondition($rule, $this->createConditionWithForm('customers', [
            'customers' => [
                $customer->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition timespan which is valid from "([^"]+") to "([^"]+)"$/
     * @Given /^the (specific price rule) has a condition timespan which is valid from "([^"]+)" to "([^"]+)"$/
     */
    public function theProductsSpecificPriceRuleHasATimeSpanCondition(ProductSpecificPriceRuleInterface $rule, $from, $to)
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
     * @Given /^the (specific price rule "[^"]+") has a condition customer-groups with (customer-group "[^"]+")$/
     * @Given /^the (specific price rule) has a condition customer-groups with (customer-group "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasACustomerGroupCondition(ProductSpecificPriceRuleInterface $rule, CustomerGroupInterface $group)
    {
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $this->addCondition($rule, $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [
                $group->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition stores with (store "[^"]+")$/
     * @Given /^the (specific price rule) has a condition stores with (store "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasAStoreCondition(ProductSpecificPriceRuleInterface $rule, StoreInterface $store)
    {
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $this->addCondition($rule, $this->createConditionWithForm('stores', [
            'stores' => [
                $store->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition zones with (zone "[^"]+")$/
     * @Given /^the (specific price rule) has a condition zones with (zone "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasAZoneCondition(ProductSpecificPriceRuleInterface $rule, ZoneInterface $zone)
    {
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $this->addCondition($rule, $this->createConditionWithForm('zones', [
            'zones' => [
                $zone->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition currencies with (currency "[^"]+")$/
     * @Given /^the (specific price rule) has a condition currencies with (currency "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasACurrencyCondition(ProductSpecificPriceRuleInterface $rule, CurrencyInterface $currency)
    {
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $this->addCondition($rule, $this->createConditionWithForm('currencies', [
            'currencies' => [
                $currency->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a action discount-percent with ([^"]+)% discount$/
     * @Given /^the (specific price rule) has a action discount-percent with ([^"]+)% discount$/
     */
    public function theProductSpecificPriceRuleHasADiscountPercentAction(ProductSpecificPriceRuleInterface $rule, $discount)
    {
        $this->assertActionForm(DiscountPercentConfigurationType::class, 'discountPercent');

        $this->addAction($rule, $this->createActionWithForm('discountPercent', [
            'percent' => intval($discount),
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a action discount with ([^"]+) in (currency "[^"]+") off$/
     * @Given /^the (specific price rule) has a action discount with ([^"]+) in (currency "[^"]+") off$/
     */
    public function theProductSpecificPriceRuleHasADiscountAmountAction(ProductSpecificPriceRuleInterface $rule, $amount, CurrencyInterface $currency)
    {
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $this->addAction($rule, $this->createActionWithForm('discountAmount', [
            'amount' => intval($amount),
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a action discount-price of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (specific price rule) has a action discount-price of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductSpecificPriceRuleHasADiscountPriceAction(ProductSpecificPriceRuleInterface $rule, $price, CurrencyInterface $currency)
    {
        $this->assertActionForm(PriceConfigurationType::class, 'discountPrice');

        $this->addAction($rule, $this->createActionWithForm('discountPrice', [
            'price' => intval($price),
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a action price of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (specific price rule) has a action price of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductSpecificPriceRuleHasAPriceAction(ProductSpecificPriceRuleInterface $rule, $price, CurrencyInterface $currency)
    {
        $this->assertActionForm(PriceConfigurationType::class, 'price');

        $this->addAction($rule, $this->createActionWithForm('price', [
            'price' => intval($price),
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition nested with operator "([^"]+)" for (store "[^"]+") and (store "[^"]+")$/
     * @Given /^the (specific price rule) has a condition nested with operator "([^"]+)" for (store "[^"]+") and (store "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasANestedConditionWithStores(ProductSpecificPriceRuleInterface $rule, $operator, StoreInterface $store1, StoreInterface $store2)
    {
        $this->assertConditionForm(ProductSpecificPriceNestedConfigurationType::class, 'nested');

        $this->addCondition($rule, $this->createConditionWithForm('nested', [
            'operator' => $operator,
            'conditions' => [
                [
                    'type' => 'stores',
                    'configuration' => [
                        'stores' => [
                            $store1->getId(),
                        ],
                    ],
                ],
                [
                    'type' => 'stores',
                    'configuration' => [
                        'stores' => [
                            $store2->getId(),
                        ],
                    ],
                ],
            ],
        ]));
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition nested with operator "([^"]+)" for (store "[^"]+") and (country "[^"]+")$/
     * @Given /^the (specific price rule) has a condition nested with operator "([^"]+)" for (store "[^"]+") and (country "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasANestedConditionWithStoreAndCountry(ProductSpecificPriceRuleInterface $rule, $operator, StoreInterface $store, CountryInterface $country)
    {
        $this->assertConditionForm(ProductSpecificPriceNestedConfigurationType::class, 'nested');

        $this->addCondition($rule, $this->createConditionWithForm('nested', [
            'operator' => $operator,
            'conditions' => [
                [
                    'type' => 'stores',
                    'configuration' => [
                        'stores' => [
                            $store->getId(),
                        ],
                    ],
                ],
                [
                    'type' => 'countries',
                    'configuration' => [
                        'countries' => [
                            $country->getId(),
                        ],
                    ],
                ],
            ],
        ]));
    }

    /**
     * @param ProductSpecificPriceRuleInterface $rule
     * @param ConditionInterface                $condition
     */
    private function addCondition(ProductSpecificPriceRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param ProductSpecificPriceRuleInterface $rule
     * @param ActionInterface                   $action
     */
    private function addAction(ProductSpecificPriceRuleInterface $rule, ActionInterface $action)
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
        return ProductSpecificPriceRuleConditionType::class;
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
        return ProductSpecificPriceRuleActionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormFactory()
    {
        return $this->formFactory;
    }
}
