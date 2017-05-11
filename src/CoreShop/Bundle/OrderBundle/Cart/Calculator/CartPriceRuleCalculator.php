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

namespace CoreShop\Bundle\OrderBundle\Cart\Calculator;

use CoreShop\Component\Order\Cart\Calculator\CartDiscountCalculatorInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

class CartPriceRuleCalculator implements CartDiscountCalculatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $actionServiceRegistry;

    /**
     * @var RuleValidationProcessorInterface
     */
    protected $ruleValidationProcessor;

    /**
     * @param ServiceRegistryInterface         $actionServiceRegistry
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     */
    public function __construct(
        ServiceRegistryInterface $actionServiceRegistry,
        RuleValidationProcessorInterface $ruleValidationProcessor
    ) {
        $this->actionServiceRegistry = $actionServiceRegistry;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $withTax = true)
    {
        Assert::isInstanceOf($subject, CartInterface::class);

        $discount = 0;

        /**
         * @var CartInterface
         * @var $rules        RuleInterface[]
         */
        $rules = $subject->getPriceRules();

        foreach ($rules as $rule) {
            if ($this->ruleValidationProcessor->isValid($subject, $rule)) {
                foreach ($rule->getActions() as $action) {
                    $processor = $this->actionServiceRegistry->get($action->getType());

                    if ($processor instanceof CartPriceRuleActionProcessorInterface) {
                        $discount += $processor->getDiscount($subject, $withTax, $action->getConfiguration());
                    }
                }
            }
        }

        return $discount;
    }
}
