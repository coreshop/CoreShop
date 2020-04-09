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
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Webmozart\Assert\Assert;

final class ProductQuantityPriceRuleContext implements Context
{
    use ConditionFormTrait;

    private $sharedStorage;
    private $objectManager;
    private $rangeFactory;
    private $formFactory;
    private $conditionFormTypeRegistry;
    private $productQuantityPriceRuleFactory;
    private $productRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $rangeFactory,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $conditionFormTypeRegistry,
        FactoryInterface $productQuantityPriceRuleFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->rangeFactory = $rangeFactory;
        $this->formFactory = $formFactory;
        $this->conditionFormTypeRegistry = $conditionFormTypeRegistry;
        $this->productQuantityPriceRuleFactory = $productQuantityPriceRuleFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @Given /^adding a quantity price rule to (product "[^"]+") named "([^"]+)" and with calculation-behaviour "([^"]+)"$/
     * @Given /^adding a quantity price rule to this (product) named "([^"]+)" with calculation-behaviour "([^"]+)"$/
     */
    public function addingAProductQuantityPriceRuleToProduct(ProductInterface $product, $ruleName, $calculationBehaviourName)
    {
        /**
         * @var ProductQuantityPriceRuleInterface $rule
         */
        $rule = $this->productQuantityPriceRuleFactory->createNew();
        $rule->setName($ruleName);
        $rule->setProduct($product->getId());
        $rule->setCalculationBehaviour($calculationBehaviourName);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();

        $this->sharedStorage->set('product-quantity-price-rule', $rule);
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") is active$/
     * @Given /^the (quantity price rule) is active$/
     */
    public function theProductQuantityPriceRuleIsActive(ProductQuantityPriceRuleInterface $rule)
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") is inactive$/
     * @Given /^the (quantity price rule) is inactive$/
     */
    public function theProductQuantityPriceRuleIsInActive(ProductQuantityPriceRuleInterface $rule)
    {
        $rule->setActive(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a range starting from ([^"]+) with behaviour percentage-decrease of ([^"]+)%$/
     * @Given /^the (quantity price rule) has a range starting from ([^"]+) with behaviour percentage-decrease of ([^"]+)%$/
     */
    public function theProductQuantityPriceRuleHasRangePercentageDecrease(
        ProductQuantityPriceRuleInterface $rule,
        int $from,
        float $percentage
    )
    {
        /**
         * @var QuantityRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour('percentage_decrease');
        $range->setPercentage($percentage);
        $range->setRangeStartingFrom($from);

        $this->addRange($rule, $range);

        $this->sharedStorage->set('quantity-price-rule-range', $range);
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a range starting from to ([^"]+) with behaviour percentage-increase of ([^"]+)%$/
     * @Given /^the (quantity price rule) has a range starting from ([^"]+) with behaviour percentage-increase of ([^"]+)%$/
     */
    public function theProductQuantityPriceRuleHasRangePercentageIncrease(
        ProductQuantityPriceRuleInterface $rule,
        int $from,
        float $percentage
    )
    {
        /**
         * @var QuantityRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour('percentage_increase');
        $range->setPercentage($percentage);
        $range->setRangeStartingFrom($from);

        $this->addRange($rule, $range);

        $this->sharedStorage->set('quantity-price-rule-range', $range);
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a range starting from ([^"]+) with behaviour amount-decrease of (\d+) in (currency "[^"]+")$/
     * @Given /^the (quantity price rule) has a range starting from ([^"]+) with behaviour amount-decrease of (\d+) in (currency "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasRangeAmountDecrease(
        ProductQuantityPriceRuleInterface $rule,
        int $from,
        int $amount,
        CurrencyInterface $currency
    ) {
        /**
         * @var QuantityRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour('amount_decrease');
        $range->setAmount($amount);
        $range->setRangeStartingFrom($from);
        $range->setCurrency($currency);

        $this->addRange($rule, $range);

        $this->sharedStorage->set('quantity-price-rule-range', $range);
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a range starting from ([^"]+) with behaviour amount-increase of (\d+) in (currency "[^"]+")$/
     * @Given /^the (quantity price rule) has a range starting from ([^"]+) with behaviour amount-increase of (\d+) in (currency "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasRangeAmountIncrease(
        ProductQuantityPriceRuleInterface $rule,
        int $from,
        int $amount,
        CurrencyInterface $currency
    ) {
        /**
         * @var QuantityRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour('amount_increase');
        $range->setAmount($amount);
        $range->setRangeStartingFrom($from);
        $range->setCurrency($currency);

        $this->addRange($rule, $range);

        $this->sharedStorage->set('quantity-price-rule-range', $range);
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a range starting from ([^"]+) with behaviour fixed of (\d+) in (currency "[^"]+")$/
     * @Given /^the (quantity price rule) has a range starting from ([^"]+) with behaviour fixed of (\d+) in (currency "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasRangeFixed(
        ProductQuantityPriceRuleInterface $rule,
        int $from,
        int $amount,
        CurrencyInterface $currency
    )
    {
        /**
         * @var QuantityRangeInterface $range
         */
        $range = $this->rangeFactory->createNew();
        $range->setPricingBehaviour('fixed');
        $range->setAmount($amount);
        $range->setRangeStartingFrom($from);
        $range->setCurrency($currency);

        $this->addRange($rule, $range);

        $this->sharedStorage->set('quantity-price-rule-range', $range);
    }

    /**
     * @Given /^the (price range) is only valid for (unit "[^"]+")$/
     */
    public function theQuantityPriceRangeIsValidForUnit(QuantityRangeInterface $range, ProductUnitInterface $unit)
    {
        $productId = $range->getRule()->getProduct();
        /**
         * @var ProductInterface $product
         */
        $product = $this->productRepository->find($productId);

        Assert::notNull($product);

        $unitDefinition = $this->findUnitDefinition($product, $unit);

        $range->setUnitDefinition($unitDefinition);

        $this->objectManager->persist($range);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a condition countries with (country "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition countries with (country "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasACountriesCondition(
        ProductQuantityPriceRuleInterface $rule,
        CountryInterface $country
    )
    {
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $this->addCondition($rule, $this->createConditionWithForm('countries', [
            'countries' => [
                $country->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a condition customers with (customer "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition customers with (customer "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasACustomerCondition(
        ProductQuantityPriceRuleInterface $rule,
        CustomerInterface $customer
    )
    {
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $this->addCondition($rule, $this->createConditionWithForm('customers', [
            'customers' => [
                $customer->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a condition timespan which is valid from "([^"]+") to "([^"]+)"$/
     * @Given /^the (quantity price rule) has a condition timespan which is valid from "([^"]+)" to "([^"]+)"$/
     */
    public function theProductQuantityPriceRuleHasATimeSpanCondition(ProductQuantityPriceRuleInterface $rule, $from, $to)
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
     * @Given /^the (quantity price rule "[^"]+") has a condition customer-groups with (customer-group "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition customer-groups with (customer-group "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasACustomerGroupCondition(
        ProductQuantityPriceRuleInterface $rule,
        CustomerGroupInterface $group
    )
    {
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $this->addCondition($rule, $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [
                $group->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a condition stores with (store "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition stores with (store "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasAStoreCondition(
        ProductQuantityPriceRuleInterface $rule,
        StoreInterface $store
    )
    {
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $this->addCondition($rule, $this->createConditionWithForm('stores', [
            'stores' => [
                $store->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a condition zones with (zone "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition zones with (zone "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasAZoneCondition(
        ProductQuantityPriceRuleInterface $rule,
        ZoneInterface $zone
    )
    {
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $this->addCondition($rule, $this->createConditionWithForm('zones', [
            'zones' => [
                $zone->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a condition currencies with (currency "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition currencies with (currency "[^"]+")$/
     */
    public function theProductsQuantityPriceRuleHasACurrencyCondition(
        ProductQuantityPriceRuleInterface $rule,
        CurrencyInterface $currency
    )
    {
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $this->addCondition($rule, $this->createConditionWithForm('currencies', [
            'currencies' => [
                $currency->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (quantity price rule "[^"]+") has a condition nested with operator "([^"]+)" for (store "[^"]+") and (store "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition nested with operator "([^"]+)" for (store "[^"]+") and (store "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasANestedConditionWithStores(
        ProductQuantityPriceRuleInterface $rule,
        $operator,
        StoreInterface $store1,
        StoreInterface $store2
    ) {
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
     * @Given /^the (quantity price rule "[^"]+") has a condition nested with operator "([^"]+)" for (store "[^"]+") and (country "[^"]+")$/
     * @Given /^the (quantity price rule) has a condition nested with operator "([^"]+)" for (store "[^"]+") and (country "[^"]+")$/
     */
    public function theProductQuantityPriceRuleHasANestedConditionWithStoreAndCountry(
        ProductQuantityPriceRuleInterface $rule,
        $operator,
        StoreInterface $store,
        CountryInterface $country
    ) {
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
     * @param ProductQuantityPriceRuleInterface $rule
     * @param ConditionInterface                $condition
     */
    private function addCondition(ProductQuantityPriceRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param ProductQuantityPriceRuleInterface $rule
     * @param QuantityRangeInterface            $range
     */
    private function addRange(ProductQuantityPriceRuleInterface $rule, QuantityRangeInterface $range)
    {
        $rule->addRange($range);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    protected function findUnitDefinition(ProductInterface $product, ProductUnitInterface $unit)
    {
        $unitDefinition = null;

        Assert::notNull($product->getUnitDefinitions());

        foreach ($product->getUnitDefinitions()->getUnitDefinitions() as $definition) {
            if ($definition->getUnit()->getId() === $unit->getId()) {
                $unitDefinition = $definition;
                break;
            }
        }

        Assert::notNull($unitDefinition);

        return $unitDefinition;
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
