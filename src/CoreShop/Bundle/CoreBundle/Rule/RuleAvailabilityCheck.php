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
use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRule;
use CoreShop\Component\Product\Repository\ProductPriceRuleRepositoryInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Model\DataObject\CoreShopCart;
use Pimcore\Model\DataObject\CoreShopProduct;
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
     * @var CartPriceRuleValidationProcessorInterface
     */
    protected $cartPriceRuleValidationProcessor;

    /**
     * @var RuleValidationProcessorInterface
     */
    protected $productPriceRuleValidationProcessor;

    /**
     * @var RuleValidationProcessorInterface
     */
    protected $productSpecificPriceRuleValidationProcessor;

    /**
     * @var RuleValidationProcessorInterface
     */
    protected $shippingRuleValidationProcessor;

    /**
     * RuleAvailabilityCheck constructor.
     *
     * @param EventDispatcherInterface                    $eventDispatcher
     * @param EntityManagerInterface                      $entityManager
     * @param CartPriceRuleRepositoryInterface            $cartPriceRuleRepository
     * @param ProductPriceRuleRepositoryInterface         $productPriceRuleRepository
     * @param ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository
     * @param RepositoryInterface                         $shippingRuleRepository
     * @param CartPriceRuleValidationProcessorInterface   $cartPriceRuleValidationProcessor
     * @param RuleValidationProcessorInterface            $productPriceRuleValidationProcessor
     * @param RuleValidationProcessorInterface            $productSpecificPriceRuleValidationProcessor
     * @param RuleValidationProcessorInterface            $shippingRuleValidationProcessor
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        CartPriceRuleRepositoryInterface $cartPriceRuleRepository,
        ProductPriceRuleRepositoryInterface $productPriceRuleRepository,
        ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository,
        RepositoryInterface $shippingRuleRepository,
        CartPriceRuleValidationProcessorInterface $cartPriceRuleValidationProcessor,
        RuleValidationProcessorInterface $productPriceRuleValidationProcessor,
        RuleValidationProcessorInterface $productSpecificPriceRuleValidationProcessor,
        RuleValidationProcessorInterface $shippingRuleValidationProcessor
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->cartPriceRuleRepository = $cartPriceRuleRepository;
        $this->productPriceRuleRepository = $productPriceRuleRepository;
        $this->productSpecificPriceRuleRepository = $productSpecificPriceRuleRepository;
        $this->shippingRuleRepository = $shippingRuleRepository;
        $this->cartPriceRuleValidationProcessor = $cartPriceRuleValidationProcessor;
        $this->productPriceRuleValidationProcessor = $productPriceRuleValidationProcessor;
        $this->productSpecificPriceRuleValidationProcessor = $productSpecificPriceRuleValidationProcessor;
        $this->shippingRuleValidationProcessor = $shippingRuleValidationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function check($params = [])
    {
        /** @var CartPriceRuleInterface $priceRule */
        foreach ($this->cartPriceRuleRepository->findBy(['active' => true]) as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->cartPriceRuleValidationProcessor->isValid(new CoreShopCart(), $filteredPriceRule, [
                'cartPriceRule' => $filteredPriceRule,
                'voucher'       => null
            ]);
            $this->processRule($priceRule, $ruleIsAvailable);
        }

        /** @var ProductPriceRuleInterface $priceRule */
        foreach ($this->productPriceRuleRepository->findBy(['active' => true]) as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->productPriceRuleValidationProcessor->isValid(new CoreShopProduct(), $filteredPriceRule);
            $this->processRule($priceRule, $ruleIsAvailable);
        }

        /** @var ProductSpecificPriceRule $priceRule */
        foreach ($this->productSpecificPriceRuleRepository->findBy(['active' => true]) as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->productSpecificPriceRuleValidationProcessor->isValid(new CoreShopProduct(), $filteredPriceRule);
            $this->processRule($priceRule, $ruleIsAvailable);
        }

        /** @var ShippingRuleInterface $priceRule */
        foreach ($this->shippingRuleRepository->findAll() as $priceRule) {
            $filteredPriceRule = $this->filterRules($priceRule);
            $ruleIsAvailable = $this->shippingRuleValidationProcessor->isValid(new Carrier(), $filteredPriceRule);
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
     * @param RuleInterface $priceRule
     * @return RuleInterface
     */
    private function filterRules(RuleInterface $priceRule)
    {
        /** @var Condition $rule */
        foreach ($priceRule->getConditions() as $id => $condition) {
            if ($condition->getType() === 'timespan') {
                $isFutureTimeSpanCondition = $this->isFutureTimeSpanCondition($condition);
                if ($isFutureTimeSpanCondition === true) {
                    $priceRule->removeCondition($condition);
                }
            } else {
                $priceRule->removeCondition($condition);
            }
        }

        return $priceRule;
    }

    /**
     * @param Condition $condition
     * @return bool
     */
    private function isFutureTimeSpanCondition(Condition $condition)
    {
        $configuration = $condition->getConfiguration();

        $dateFrom = Carbon::createFromTimestamp($configuration['dateFrom'] / 1000);
        $date = Carbon::now();

        if ($configuration['dateFrom'] > 0) {
            if ($dateFrom->getTimestamp() > $date->getTimestamp()) {
                return true;
            }
        }

        return false;
    }
}