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

namespace CoreShop\Component\Product\Rule\Calculator;

use CoreShop\Component\Product\Calculator\ProductDiscountCalculatorInterface;
use CoreShop\Component\Product\Calculator\ProductDiscountPriceCalculatorInterface;
use CoreShop\Component\Product\Calculator\ProductRetailPriceCalculatorInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Rule\Action\ProductDiscountActionProcessorInterface;
use CoreShop\Component\Product\Rule\Action\ProductDiscountPriceActionProcessorInterface;
use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;
use CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

final class ProductPriceRuleCalculator implements ProductDiscountCalculatorInterface, ProductRetailPriceCalculatorInterface, ProductDiscountPriceCalculatorInterface
{
    /**
     * @var ValidRulesFetcherInterface
     */
    private $validRulesFetcher;

    /**
     * @var ServiceRegistryInterface
     */
    private $actionServiceRegistry;

    /**
     * @param ValidRulesFetcherInterface $validRulesFetcher
     * @param ServiceRegistryInterface   $actionServiceRegistry
     */
    public function __construct(
        ValidRulesFetcherInterface $validRulesFetcher,
        ServiceRegistryInterface $actionServiceRegistry
    ) {
        $this->validRulesFetcher = $validRulesFetcher;
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(ProductInterface $subject)
    {
        $price = 0;

        /**
         * @var RuleInterface[]
         */
        $rules = $this->validRulesFetcher->getValidRules($subject);

        if (is_array($rules)) {
            foreach ($rules as $rule) {
                /*
                 * @var ActionInterface
                 */
                foreach ($rule->getActions() as $action) {
                    $processor = $this->actionServiceRegistry->get($action->getType());

                    if (!$processor instanceof ProductPriceActionProcessorInterface) {
                        continue;
                    }

                    $actionPrice = $processor->getPrice($subject, $action->getConfiguration());

                    if (false !== $actionPrice && null !== $actionPrice) {
                        $price = $actionPrice;
                    }
                }
            }
        }

        return 0 === $price ? false : $price;
    }

    /**
     * @param ProductInterface $subject
     *
     * @return bool|int|mixed
     */
    public function getDiscountPrice(ProductInterface $subject)
    {
        $price = 0;

        /**
         * @var RuleInterface[]
         */
        $rules = $this->validRulesFetcher->getValidRules($subject);

        if (is_array($rules)) {
            foreach ($rules as $rule) {
                /*
                 * @var ActionInterface
                 */
                foreach ($rule->getActions() as $action) {
                    $processor = $this->actionServiceRegistry->get($action->getType());

                    if (!$processor instanceof ProductDiscountPriceActionProcessorInterface) {
                        continue;
                    }

                    $actionPrice = $processor->getDiscountPrice($subject, $action->getConfiguration());

                    if (false !== $actionPrice && null !== $actionPrice) {
                        $price = $actionPrice;
                    }
                }
            }
        }

        return 0 === $price ? false : $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(ProductInterface $subject, $price)
    {
        $discount = 0;

        /**
         * @var RuleInterface[]
         */
        $rules = $this->validRulesFetcher->getValidRules($subject);

        if (!is_array($rules)) {
            return $discount;
        }

        foreach ($rules as $rule) {
            foreach ($rule->getActions() as $action) {
                $processor = $this->actionServiceRegistry->get($action->getType());

                if (!$processor instanceof ProductDiscountActionProcessorInterface) {
                    continue;
                }

                $discount += $processor->getDiscount($subject, $price, $action->getConfiguration());
            }
        }

        return $discount;
    }
}
