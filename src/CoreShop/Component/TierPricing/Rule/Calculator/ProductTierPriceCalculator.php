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

namespace CoreShop\Component\TierPricing\Rule\Calculator;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRange;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;
use CoreShop\Component\TierPricing\Rule\Action\TierPriceActionInterface;
use CoreShop\Component\TierPricing\Rule\Fetcher\ValidRulesFetcherInterface;
use Webmozart\Assert\Assert;

final class ProductTierPriceCalculator implements ProductTierPriceCalculatorInterface
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
    public function getTierPriceRulesForProduct(TierPriceAwareInterface $subject, array $context)
    {
        /** @var ProductSpecificTierPriceRuleInterface[] $rules */
        $rules = $this->validRulesFetcher->getValidRules($subject, $context);

        if (!is_array($rules)) {
            return [];
        }

        // sort by priority! higher prio first!
        usort($rules, function (ProductSpecificTierPriceRuleInterface $a, ProductSpecificTierPriceRuleInterface $b) {
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
    public function calculateRangePrice(ProductTierPriceRangeInterface $range, TierPriceAwareInterface $subject, array $context)
    {
        $realItemPrice = 0;
        $pricingBehaviour = $range->getPricingBehaviour();

        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        /**
         * @var TierPriceActionInterface $service
         */
        $service = $this->actionRegistry->get($pricingBehaviour);

        return $service->calculate($range, $subject, $realItemPrice, $context);
    }
}
