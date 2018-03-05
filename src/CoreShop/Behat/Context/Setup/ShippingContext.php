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
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class ShippingContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var FactoryInterface
     */
    private $carrierFactory;

    /**
     * @var FactoryInterface
     */
    private $shippingRuleFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     * @param CarrierRepositoryInterface $carrierRepository
     * @param FactoryInterface $carrierFactory
     * @param FactoryInterface $shippingRuleFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        CarrierRepositoryInterface $carrierRepository,
        FactoryInterface $carrierFactory,
        FactoryInterface $shippingRuleFactory
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->carrierRepository = $carrierRepository;
        $this->carrierFactory = $carrierFactory;
        $this->shippingRuleFactory = $shippingRuleFactory;
    }

    /**
     * @Given /^the site has a carrier "([^"]+)"$/
     */
    public function theSiteHasACarrier($name)
    {
        $this->createCarrier($name);
    }

    /**
     * @Given /^adding a shipping rule named "([^"]+)"$/
     */
    public function addingAShippingRule($ruleName)
    {
        /**
         * @var $rule ShippingRuleInterface
         */
        $rule = $this->shippingRuleFactory->createNew();
        $rule->setName($ruleName);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();

        $this->sharedStorage->set('shipping-rule', $rule);
    }

    /**
     * @Given /^the (shipping rule "[^"]+") has a condition amount from "([^"]+)" to "([^"]+)"$/
     * @Given /^the (shipping rule) has a condition amount from "([^"]+)" to "([^"]+)"$/
     */
    public function theShippingRuleHasAAmountCondition(ShippingRuleInterface $rule, $minAmount, $maxAmount)
    {
        $configuration = [
            'minAmount' => $minAmount,
            'maxAmount' => $maxAmount
        ];

        $condition = new Condition();
        $condition->setType('amount');
        $condition->setConfiguration($configuration);

        $this->addCondition($rule, $condition);
    }

    /**
     * @param $name
     */
    private function createCarrier($name)
    {
        /**
         * @var $carrier CarrierInterface
         */
        $carrier = $this->carrierFactory->createNew();
        $carrier->setName($name);

        $this->saveCarrier($carrier);
    }

    /**
     * @param CarrierInterface $carrier
     */
    private function saveCarrier(CarrierInterface $carrier)
    {
        $this->objectManager->persist($carrier);
        $this->objectManager->flush();

        $this->sharedStorage->set('carrier', $carrier);
    }

    /**
     * @param ShippingRuleInterface $rule
     * @param ConditionInterface $condition
     */
    private function addCondition(ShippingRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param ShippingRuleInterface $rule
     * @param ActionInterface $action
     */
    private function addAction(ShippingRuleInterface $rule, ActionInterface $action)
    {
        $rule->addAction($action);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }
}
