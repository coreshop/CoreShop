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

namespace CoreShop\Component\ProductQuantityPriceRules\Rule\Calculator;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Fetcher\ValidRulesFetcherInterface;
use Webmozart\Assert\Assert;

final class ProductQuantityRangePriceCalculator implements ProductQuantityRangePriceCalculatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $actionRegistry;

    /**
     * @var ValidRulesFetcherInterface
     */
    private $validRulesFetcher;

    /**
     * @param ServiceRegistryInterface   $actionRegistry
     * @param ValidRulesFetcherInterface $validRulesFetcher
     */
    public function __construct(ServiceRegistryInterface $actionRegistry, ValidRulesFetcherInterface $validRulesFetcher)
    {
        $this->actionRegistry = $actionRegistry;
        $this->validRulesFetcher = $validRulesFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRulesForProduct(QuantityRangePriceAwareInterface $subject, array $context)
    {
        /** @var ProductQuantityPriceRuleInterface[] $rules */
        $rules = $this->validRulesFetcher->getValidRules($subject, $context);

        if (!is_array($rules)) {
            return [];
        }

        // sort by priority! higher prio first!
        usort($rules, function (ProductQuantityPriceRuleInterface $a, ProductQuantityPriceRuleInterface $b) {
            if ($a->getPriority() === $b->getPriority()) {
                return 0;
            }

            return ($a->getPriority() > $b->getPriority()) ? -1 : 1;
        });

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $subject, array $context)
    {
        $realItemPrice = 0;
        $pricingBehaviour = $range->getPricingBehaviour();

        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        /**
         * @var ProductQuantityPriceRuleActionInterface $service
         */
        $service = $this->actionRegistry->get($pricingBehaviour);

        return $service->calculate($range, $subject, $realItemPrice, $context);
    }
}
