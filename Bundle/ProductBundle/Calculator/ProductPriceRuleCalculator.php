<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class ProductPriceRuleCalculator implements ProductPriceCalculatorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $productPriceRuleRepository;

    /**
     * @var RuleValidationProcessorInterface
     */
    protected $ruleValidationProcessor;

    /**
     * @var ServiceRegistryInterface
     */
    protected $actionServiceRegistry;

    /**
     * @param RepositoryInterface              $productPriceRuleRepository
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     * @param ServiceRegistryInterface         $actionServiceRegistry
     */
    public function __construct(
        RepositoryInterface $productPriceRuleRepository,
        RuleValidationProcessorInterface $ruleValidationProcessor,
        ServiceRegistryInterface $actionServiceRegistry
    ) {
        $this->productPriceRuleRepository = $productPriceRuleRepository;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($subject)
    {
        $price = 0;

        /**
         * @var RuleInterface[]
         */
        $rules = $this->productPriceRuleRepository->findAll();

        foreach ($rules as $rule) {
            if ($this->ruleValidationProcessor->isValid($subject, $rule)) {
                foreach ($rule->getActions() as $action) {
                    $processor = $this->actionServiceRegistry->get($action->getType());

                    if ($processor instanceof ProductPriceActionProcessorInterface) {
                        $actionPrice = $processor->getPrice($subject, $action->getConfiguration());

                        if (!empty($actionPrice)) {
                            $price = $actionPrice;
                        }
                    }
                }
            }
        }

        return $price === 0 ? false : $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $price)
    {
        $discount = 0;

        /**
         * @var RuleInterface[]
         */
        $rules = $this->productPriceRuleRepository->findAll();

        foreach ($rules as $rule) {
            if ($this->ruleValidationProcessor->isValid($subject, $rule)) {
                foreach ($rule->getActions() as $action) {
                    $processor = $this->actionServiceRegistry->get($action->getType());

                    if ($processor instanceof ProductPriceActionProcessorInterface) {
                        $discount += $processor->getDiscount($subject, $price, $action->getConfiguration());
                    }
                }
            }
        }

        return $discount;
    }
}
