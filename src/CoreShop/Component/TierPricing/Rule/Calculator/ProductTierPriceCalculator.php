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
use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRange;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;
use CoreShop\Component\TierPricing\Rule\Fetcher\ValidRulesFetcherInterface;
use Webmozart\Assert\Assert;

final class ProductTierPriceCalculator implements ProductTierPriceCalculatorInterface
{
    /**
     * @var CurrencyConverterInterface
     */
    protected $moneyConverter;

    /**
     * @var ValidRulesFetcherInterface
     */
    private $validRulesFetcher;

    /**
     * @param CurrencyConverterInterface $moneyConverter
     * @param ValidRulesFetcherInterface $validRulesFetcher
     */
    public function __construct(CurrencyConverterInterface $moneyConverter, ValidRulesFetcherInterface $validRulesFetcher)
    {
        $this->moneyConverter = $moneyConverter;
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
        $price = 0;
        $pricingBehaviour = $range->getPricingBehaviour();

        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        if ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_FIXED) {
            return $this->calculateFixed($realItemPrice, $range, $context);
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_AMOUNT_DISCOUNT) {
            return $this->calculateAmountDiscount($realItemPrice, $range, $context);
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_AMOUNT_INCREASE) {
            return $this->calculateAmountIncrease($realItemPrice, $range, $context);
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_PERCENTAGE_DISCOUNT) {
            return $this->calculatePercentageDiscount($realItemPrice, $range, $context);
        } elseif ($pricingBehaviour === ProductTierPriceRange::PRICING_BEHAVIOUR_PERCENTAGE_INCREASE) {
            return $this->calculatePercentageIncrease($realItemPrice, $range, $context);
        }

        return $price;
    }

    /**
     * @param int                            $realItemPrice
     * @param ProductTierPriceRangeInterface $range
     * @param array                          $context
     *
     * @return int
     */
    private function calculateFixed(int $realItemPrice, ProductTierPriceRangeInterface $range, array $context)
    {
        Assert::isInstanceOf($range->getCurrency(), CurrencyInterface::class);
        $currentContextCurrency = $context['currency'];
        return $this->moneyConverter->convert($range->getAmount(), $range->getCurrency()->getIsoCode(), $currentContextCurrency->getIsoCode());
    }

    /**
     * @param int                            $realItemPrice
     * @param ProductTierPriceRangeInterface $range
     * @param array                          $context
     *
     * @return mixed
     */
    private function calculateAmountDiscount(int $realItemPrice, ProductTierPriceRangeInterface $range, array $context)
    {
        Assert::isInstanceOf($range->getCurrency(), CurrencyInterface::class);
        $currentContextCurrency = $context['currency'];
        $currencyAwareAmount = $this->moneyConverter->convert($range->getAmount(), $range->getCurrency()->getIsoCode(), $currentContextCurrency->getIsoCode());

        return max($realItemPrice - $currencyAwareAmount, 0);
    }

    /**
     * @param int                            $realItemPrice
     * @param ProductTierPriceRangeInterface $range
     * @param array                          $context
     *
     * @return int
     */
    private function calculateAmountIncrease(int $realItemPrice, ProductTierPriceRangeInterface $range, array $context)
    {
        Assert::isInstanceOf($range->getCurrency(), CurrencyInterface::class);
        $currentContextCurrency = $context['currency'];
        $currencyAwareAmount = $this->moneyConverter->convert($range->getAmount(), $range->getCurrency()->getIsoCode(), $currentContextCurrency->getIsoCode());

        return $realItemPrice + $currencyAwareAmount;
    }

    /**
     * @param int                            $realItemPrice
     * @param ProductTierPriceRangeInterface $range
     * @param array                          $context
     *
     * @return mixed
     */
    private function calculatePercentageDiscount(int $realItemPrice, ProductTierPriceRangeInterface $range, array $context)
    {
        return max($realItemPrice - ((int)round(($range->getPercentage() / 100) * $realItemPrice)), 0);
    }

    /**
     * @param int                            $realItemPrice
     * @param ProductTierPriceRangeInterface $range
     * @param array                          $context
     *
     * @return int
     */
    private function calculatePercentageIncrease(int $realItemPrice, ProductTierPriceRangeInterface $range, array $context)
    {
        return $realItemPrice + ((int)round(($range->getPercentage() / 100) * $realItemPrice));
    }
}
