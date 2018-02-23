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
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class ProductSpecificPriceRuleContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $productSpecificPriceRuleFactory;

    /**
     * @var ProductSpecificPriceRuleRepositoryInterface
     */
    private $productSpecificPriceRuleRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     * @param FactoryInterface $productSpecificPriceRuleFactory
     * @param ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $productSpecificPriceRuleFactory,
        ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->productSpecificPriceRuleFactory = $productSpecificPriceRuleFactory;
        $this->productSpecificPriceRuleRepository = $productSpecificPriceRuleRepository;
    }

    /**
     * @Given /^adding a product specific price rule to (product "[^"]+") named "([^"]+)"$/
     */
    public function addingAProductSpecificPriceRuleToProduct(ProductInterface $product, $ruleName)
    {
        /**
         * @var $rule ProductSpecificPriceRuleInterface
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
     * @Given /^([^"]+) is active$/
     */
    public function theProductsSpecificPriceRuleIsActive(ProductSpecificPriceRuleInterface $rule)
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (specific price rule "[^"]+") is inactive$/
     * @Given /^([^"]+) is inactive$/
     */
    public function theProductsSpecificPriceRuleIsInActive(ProductSpecificPriceRuleInterface $rule)
    {
        $rule->setActive(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition countries with (country "[^"]+")$/
     * @Given /^([^"]+) has a condition countries with (country "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasACategoryCondition(ProductSpecificPriceRuleInterface $rule, CountryInterface $country)
    {
        $configuration = [
            'countries' => [
                $country->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('countries');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition customers with (customer "[^"]+")$/
     * @Given /^([^"]+) has a condition customers with (customer "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasACustomerCondition(ProductSpecificPriceRuleInterface $rule, CustomerInterface $customer)
    {
        $configuration = [
            'customers' => [
                $customer->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('customers');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition timespan which is valid from "([^"]+") to "([^"]+)"$/
     * @Given /^([^"]+) has a condition timespan which is valid from "([^"]+)" to "([^"]+)"$/
     */
    public function theProductsSpecificPriceRuleHasATimeSpanCondition(ProductSpecificPriceRuleInterface $rule, $from, $to)
    {
        $from = new \DateTime($from);
        $to = new \DateTime($to);

        $configuration = [
            'dateFrom' => $from->getTimestamp() * 1000,
            'dateTo' => $to->getTimestamp() * 1000
        ];

        $condition = new Condition();
        $condition->setType('timespan');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @Given /^the (specific price rule "[^"]+") has a condition customer-groups with (customer-group "[^"]+")$/
     * @Given /^([^"]+) has a condition customer-groups with (customer-group "[^"]+")$/
     */
    public function theProductsSpecificPriceRuleHasACustomerGroupCondition(ProductSpecificPriceRuleInterface $rule, CustomerGroupInterface $group)
    {
        $configuration = [
            'customerGroups' => [
                $group->getId()
            ]
        ];

        $condition = new Condition();
        $condition->setType('customerGroups');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @param ProductSpecificPriceRuleInterface $rule
     * @param ConditionInterface $condition
     */
    private function addCondition(ProductSpecificPriceRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }
}
