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

use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRange;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;
use CoreShop\Component\TierPricing\Rule\Fetcher\ValidRulesFetcherInterface;

final class ProductTierPriceCalculator implements ProductTierPriceCalculatorInterface
{
    /**
     * @var ValidRulesFetcherInterface
     */
    private $validRulesFetcher;

    /**
     * @param ValidRulesFetcherInterface $validRulesFetcher
     */
    public function __construct(ValidRulesFetcherInterface $validRulesFetcher)
    {
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
    public function calculateRangePrice(
        ProductTierPriceRangeInterface $range,
        TierPriceAwareInterface $subject,
        array $context
    ) {
        $realItemPrice = 0;
        $price = 0;
        $pricingBehaviour = $range->getPricingBehaviour();

        if ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_FIXED) {
            // @todo: calculate with currency?
            $price = $range->getAmount();
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_AMOUNT_DISCOUNT) {
            // @todo: calculate with currency?
            $price = max($realItemPrice - $range->getAmount(), 0);
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_AMOUNT_INCREASE) {
            // @todo: calculate with currency?
            $price = $realItemPrice + $range->getAmount();
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_PERCENTAGE_DISCOUNT) {
            $price = max($realItemPrice - ((int)round(($range->getPercentage() / 100) * $realItemPrice)), 0);
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_PERCENTAGE_INCREASE) {
            $price = $realItemPrice + ((int)round(($range->getPercentage() / 100) * $realItemPrice));
        }

        return $price;
    }
}
