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

use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface;
use CoreShop\Component\TierPricing\Locator\TierPriceLocatorInterface;
use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;

final class ProductTierPriceCalculator implements ProductTierPriceCalculatorInterface
{
    /**
     * @var ValidRulesFetcherInterface
     */
    private $validRulesFetcher;

    /**
     * @var TierPriceLocatorInterface
     */
    private $tierPriceLocator;

    /**
     * @param ValidRulesFetcherInterface $validRulesFetcher
     * @param TierPriceLocatorInterface  $tierPriceLocator
     */
    public function __construct(
        ValidRulesFetcherInterface $validRulesFetcher,
        TierPriceLocatorInterface $tierPriceLocator
    ) {
        $this->validRulesFetcher = $validRulesFetcher;
        $this->tierPriceLocator = $tierPriceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getTierPriceRulesForProduct(ProductInterface $subject, array $context)
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
    public function getTierPriceForCartItem(ProductInterface $subject, CartItemInterface $cartItem, array $context)
    {
        $price = 0;

        /** @var ProductSpecificTierPriceRuleInterface[] $rules */
        $tierPriceRules = $this->getTierPriceRulesForProduct($subject, $context);

        if (!is_array($tierPriceRules)) {
            return false;
        }

        if (count($tierPriceRules) === 0) {
            return false;
        }

        $tierPriceRule = $tierPriceRules[0];
        $locatedTierPrice = $this->tierPriceLocator->locate($tierPriceRule->getRanges(), $cartItem->getQuantity());

        if ($locatedTierPrice instanceof ProductTierPriceRangeInterface) {
            $price = $locatedTierPrice->getPrice();
        }

        return $price === 0 ? false : $price;
    }
}
