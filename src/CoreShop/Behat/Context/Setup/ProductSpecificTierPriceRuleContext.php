<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleConditionType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\ProductSpecificPriceNestedConfigurationType;
use CoreShop\Bundle\ProductBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;

final class ProductSpecificTierPriceRuleContext implements Context
{
    use ConditionFormTrait;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FactoryInterface
     */
    private $rangeFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormTypeRegistryInterface
     */
    private $conditionFormTypeRegistry;

    /**
     * @var FactoryInterface
     */
    private $productSpecificTierPriceRuleFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     * @param FactoryInterface $rangeFactory
     * @param FormFactoryInterface $formFactory
     * @param FormTypeRegistryInterface $conditionFormTypeRegistry
     * @param FactoryInterface $productSpecificTierPriceRuleFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $rangeFactory,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $conditionFormTypeRegistry,
        FactoryInterface $productSpecificTierPriceRuleFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->rangeFactory = $rangeFactory;
        $this->formFactory = $formFactory;
        $this->conditionFormTypeRegistry = $conditionFormTypeRegistry;
        $this->productSpecificTierPriceRuleFactory = $productSpecificTierPriceRuleFactory;
    }


    /**
     * @Given /^adding a product specific tier price rule to (product "[^"]+") named "([^"]+)"$/
     */
    public function addingAProductSpecificPriceRuleToProduct(ProductInterface $product, $ruleName)
    {
        /**
         * @var ProductSpecificTierPriceRuleInterface $rule
         */
        $rule = $this->productSpecificTierPriceRuleFactory->createNew();
        $rule->setName($ruleName);
        $rule->setProduct($product->getId());

        $this->objectManager->persist($rule);
        $this->objectManager->flush();

        $this->sharedStorage->set('product-specific-tier-price-rule', $rule);
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") is active$/
     * @Given /^the (specific tier price rule) is active$/
     */
    public function theProductsSpecificTierPriceRuleIsActive(ProductSpecificTierPriceRuleInterface $rule)
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") is inactive$/
     * @Given /^the (specific tier price rule) is inactive$/
     */
    public function theProductsSpecificTierPriceRuleIsInActive(ProductSpecificTierPriceRuleInterface $rule)
    {
        $rule->setActive(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a range from ([^"]+) to ([^"]+) with behaviour percentage-decrease of ([^"]+)%$/
     * @Given /^the (specific tier price rule) has a range from ([^"]+) to ([^"]+) with behaviour percentage-decrease of ([^"]+)%$/
     */
    public function theProductsSpecificTierPriceRuleHasRangePercentageDecrease(ProductSpecificTierPriceRuleInterface $rule, int $from, int $to, $percentage)
    {
        /**
         * @var ProductTierPriceRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour("percentage_decrease");
        $range->setPercentage($percentage);
        $range->setRangeFrom($from);
        $range->setRangeTo($to);

        $this->addRange($rule, $range);
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a range from ([^"]+) to ([^"]+) with behaviour percentage-increase of ([^"]+)%$/
     * @Given /^the (specific tier price rule) has a range from ([^"]+) to ([^"]+) with behaviour percentage-increase of ([^"]+)%$/
     */
    public function theProductsSpecificTierPriceRuleHasRangePercentageIncrease(ProductSpecificTierPriceRuleInterface $rule, int $from, int $to, $percentage)
    {
        /**
         * @var ProductTierPriceRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour("percentage_increase");
        $range->setPercentage($percentage);
        $range->setRangeFrom($from);
        $range->setRangeTo($to);

        $this->addRange($rule, $range);
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a range from ([^"]+) to ([^"]+) with behaviour amount-decrease of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (specific tier price rule) has a range from ([^"]+) to ([^"]+) with behaviour amount-decrease of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasRangeAmountDecrease(ProductSpecificTierPriceRuleInterface $rule, int $from, int $to, $amount, CurrencyInterface $currency)
    {
        /**
         * @var ProductTierPriceRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour("amount_decrease");
        $range->setAmount($amount);
        $range->setRangeFrom($from);
        $range->setRangeTo($to);
        $range->setCurrency($currency);

        $this->addRange($rule, $range);
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a range from ([^"]+) to ([^"]+) with behaviour amount-increase of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (specific tier price rule) has a range from ([^"]+) to ([^"]+) with behaviour amount-increase of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasRangeAmountIncrease(ProductSpecificTierPriceRuleInterface $rule, int $from, int $to, $amount, CurrencyInterface $currency)
    {
        /**
         * @var ProductTierPriceRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour("amount_increase");
        $range->setAmount($amount);
        $range->setRangeFrom($from);
        $range->setRangeTo($to);
        $range->setCurrency($currency);

        $this->addRange($rule, $range);
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a range from ([^"]+) to ([^"]+) with behaviour fixed of ([^"]+) in (currency "[^"]+")$/
     * @Given /^the (specific tier price rule) has a range from ([^"]+) to ([^"]+) with behaviour fixed of ([^"]+) in (currency "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasRangeFixed(ProductSpecificTierPriceRuleInterface $rule, int $from, int $to, $amount, CurrencyInterface $currency)
    {
        /**
         * @var ProductTierPriceRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour("fixed");
        $range->setAmount($amount);
        $range->setRangeFrom($from);
        $range->setRangeTo($to);
        $range->setCurrency($currency);

        $this->addRange($rule, $range);
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a condition countries with (country "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition countries with (country "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasACountriesCondition(ProductSpecificTierPriceRuleInterface $rule, CountryInterface $country)
    {
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $this->addCondition($rule, $this->createConditionWithForm('countries', [
            'countries' => [
                $country->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a condition customers with (customer "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition customers with (customer "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasACustomerCondition(ProductSpecificTierPriceRuleInterface $rule, CustomerInterface $customer)
    {
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $this->addCondition($rule, $this->createConditionWithForm('customers', [
            'customers' => [
                $customer->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a condition timespan which is valid from "([^"]+") to "([^"]+)"$/
     * @Given /^the (specific tier price rule) has a condition timespan which is valid from "([^"]+)" to "([^"]+)"$/
     */
    public function theProductsSpecificTierPriceRuleHasATimeSpanCondition(ProductSpecificTierPriceRuleInterface $rule, $from, $to)
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
     * @Given /^the (specific tier price rule "[^"]+") has a condition customer-groups with (customer-group "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition customer-groups with (customer-group "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasACustomerGroupCondition(ProductSpecificTierPriceRuleInterface $rule, CustomerGroupInterface $group)
    {
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $this->addCondition($rule, $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [
                $group->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a condition stores with (store "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition stores with (store "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasAStoreCondition(ProductSpecificTierPriceRuleInterface $rule, StoreInterface $store)
    {
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $this->addCondition($rule, $this->createConditionWithForm('stores', [
            'stores' => [
                $store->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a condition zones with (zone "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition zones with (zone "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasAZoneCondition(ProductSpecificTierPriceRuleInterface $rule, ZoneInterface $zone)
    {
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $this->addCondition($rule, $this->createConditionWithForm('zones', [
            'zones' => [
                $zone->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a condition currencies with (currency "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition currencies with (currency "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasACurrencyCondition(ProductSpecificTierPriceRuleInterface $rule, CurrencyInterface $currency)
    {
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $this->addCondition($rule, $this->createConditionWithForm('currencies', [
            'currencies' => [
                $currency->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (specific tier price rule "[^"]+") has a condition nested with operator "([^"]+)" for (store "[^"]+") and (store "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition nested with operator "([^"]+)" for (store "[^"]+") and (store "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasANestedConditionWithStores(ProductSpecificTierPriceRuleInterface $rule, $operator, StoreInterface $store1, StoreInterface $store2)
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
     * @Given /^the (specific tier price rule "[^"]+") has a condition nested with operator "([^"]+)" for (store "[^"]+") and (country "[^"]+")$/
     * @Given /^the (specific tier price rule) has a condition nested with operator "([^"]+)" for (store "[^"]+") and (country "[^"]+")$/
     */
    public function theProductsSpecificTierPriceRuleHasANestedConditionWithStoreAndCountry(ProductSpecificTierPriceRuleInterface $rule, $operator, StoreInterface $store, CountryInterface $country)
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
     * @param ProductSpecificTierPriceRuleInterface $rule
     * @param ConditionInterface                $condition
     */
    private function addCondition(ProductSpecificTierPriceRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param ProductSpecificTierPriceRuleInterface $rule
     * @param ProductTierPriceRangeInterface        $range
     */
    private function addRange(ProductSpecificTierPriceRuleInterface $rule, ProductTierPriceRangeInterface $range)
    {
        $rule->addRange($range);

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
    protected function getFormFactory()
    {
        return $this->formFactory;
    }
}
