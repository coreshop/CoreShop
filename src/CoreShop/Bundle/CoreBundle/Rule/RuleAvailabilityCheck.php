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

namespace CoreShop\Bundle\CoreBundle\Rule;

use Carbon\Carbon;
use CoreShop\Bundle\CoreBundle\Event\RuleAvailabilityCheckEvent;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRule;
use CoreShop\Component\Product\Repository\ProductPriceRuleRepositoryInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RuleAvailabilityCheck implements RuleAvailabilityCheckInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CartPriceRuleRepositoryInterface
     */
    protected $cartPriceRuleRepository;

    /**
     * @var ProductPriceRuleRepositoryInterface
     */
    protected $productPriceRuleRepository;

    /**
     * @var ProductSpecificPriceRuleRepositoryInterface
     */
    protected $productSpecificPriceRuleRepository;

    /**
     * @var RepositoryInterface
     */
    protected $shippingRuleRepository;

    /**
     * RuleAvailabilityCheck constructor.
     *
     * @param EventDispatcherInterface                    $eventDispatcher
     * @param EntityManagerInterface                      $entityManager
     * @param CartPriceRuleRepositoryInterface            $cartPriceRuleRepository
     * @param ProductPriceRuleRepositoryInterface         $productPriceRuleRepository
     * @param ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository
     * @param RepositoryInterface                         $shippingRuleRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        CartPriceRuleRepositoryInterface $cartPriceRuleRepository,
        ProductPriceRuleRepositoryInterface $productPriceRuleRepository,
        ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository,
        RepositoryInterface $shippingRuleRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->cartPriceRuleRepository = $cartPriceRuleRepository;
        $this->productPriceRuleRepository = $productPriceRuleRepository;
        $this->productSpecificPriceRuleRepository = $productSpecificPriceRuleRepository;
        $this->shippingRuleRepository = $shippingRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function check($params = [])
    {
        /** @var CartPriceRuleInterface $priceRule */
        foreach ($this->cartPriceRuleRepository->findBy(['active' => true]) as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->ruleIsValid($filteredPriceRule);
            $this->processRule($priceRule, $ruleIsAvailable);
        }

        /** @var ProductPriceRuleInterface $priceRule */
        foreach ($this->productPriceRuleRepository->findBy(['active' => true]) as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->ruleIsValid($filteredPriceRule);
            $this->processRule($priceRule, $ruleIsAvailable);
        }

        /** @var ProductSpecificPriceRule $priceRule */
        foreach ($this->productSpecificPriceRuleRepository->findBy(['active' => true]) as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->ruleIsValid($filteredPriceRule);
            $this->processRule($priceRule, $ruleIsAvailable);
        }

        /** @var ShippingRuleInterface $priceRule */
        foreach ($this->shippingRuleRepository->findAll() as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->ruleIsValid($filteredPriceRule);
            $this->processRule($priceRule, $ruleIsAvailable);
        }
    }

    /**
     * @param RuleInterface $rule
     * @param bool          $ruleIsAvailable
     */
    private function processRule(RuleInterface $rule, bool $ruleIsAvailable)
    {
        $event = $this->eventDispatcher->dispatch(
            'coreshop.rule.availability_check',
            new RuleAvailabilityCheckEvent($rule, get_class($rule), $ruleIsAvailable)
        );

        if ($event->isAvailable() === false) {
            if ($rule instanceof ToggleableInterface) {
                $rule->setActive(false);
                $this->entityManager->persist($rule);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param RuleInterface $rule
     * @return bool
     */
    private function ruleIsValid(RuleInterface $rule)
    {
        $valid = true;

        /** @var Condition $condition */
        foreach ($rule->getConditions() as $id => $condition) {

            $configuration = $condition->getConfiguration();
            $dateFrom = Carbon::createFromTimestamp($configuration['dateFrom'] / 1000);
            $dateTo = Carbon::createFromTimestamp($configuration['dateTo'] / 1000);

            $date = Carbon::now();

            // future rule is also valid
            if ($configuration['dateFrom'] > 0) {
                if ($dateFrom->getTimestamp() > $date->getTimestamp()) {
                    continue;
                }
            }

            if ($configuration['dateFrom'] > 0) {
                if ($date->getTimestamp() < $dateFrom->getTimestamp()) {
                    $valid = false;
                    break;
                }
            }

            if ($configuration['dateTo'] > 0) {
                if ($date->getTimestamp() > $dateTo->getTimestamp()) {
                    $valid = false;
                    break;
                }
            }

        }

        return $valid;
    }

    /**
     * @param RuleInterface $rule
     * @return RuleInterface
     */
    private function filterRules(RuleInterface $rule)
    {
        /** @var Condition $condition */
        foreach ($rule->getConditions() as $id => $condition) {
            if ($condition->getType() !== 'timespan') {
                $rule->removeCondition($condition);
            }
        }

        return $rule;
    }
}