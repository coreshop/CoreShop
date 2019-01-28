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

namespace CoreShop\Component\Core\TierPricing\Rule\Calculator;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\TierPricing\Locator\TierPriceLocatorInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRange;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;
use \CoreShop\Component\TierPricing\Rule\Calculator\ProductTierPriceCalculatorInterface as BaseProductTierPriceCalculatorInterface;
use Webmozart\Assert\Assert;

final class ProductTierPriceCalculator implements ProductTierPriceCalculatorInterface
{
    /**
     * @var ProductTierPriceCalculatorInterface
     */
    private $inner;

    /**
     * @var CurrencyConverterInterface
     */
    protected $moneyConverter;

    /**
     * @var PurchasableCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var TierPriceLocatorInterface
     */
    private $tierPriceLocator;

    /**
     * @param BaseProductTierPriceCalculatorInterface $inner
     * @param CurrencyConverterInterface              $moneyConverter
     * @param PurchasableCalculatorInterface          $productPriceCalculator
     * @param TierPriceLocatorInterface               $tierPriceLocator
     */
    public function __construct(
        BaseProductTierPriceCalculatorInterface $inner,
        CurrencyConverterInterface $moneyConverter,
        PurchasableCalculatorInterface $productPriceCalculator,
        TierPriceLocatorInterface $tierPriceLocator
    ) {
        $this->inner = $inner;
        $this->moneyConverter = $moneyConverter;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->tierPriceLocator = $tierPriceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getTierPriceRulesForProduct(TierPriceAwareInterface $subject, array $context)
    {
        return $this->inner->getTierPriceRulesForProduct($subject, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getTierPriceForCartItem(
        TierPriceAwareInterface $subject,
        CartItemInterface $cartItem,
        array $context
    ) {
        $tierPriceRules = $this->getTierPriceRulesForProduct($subject, $context);

        if (!is_array($tierPriceRules)) {
            return false;
        }

        if (count($tierPriceRules) === 0) {
            return false;
        }

        $tierPriceRule = $tierPriceRules[0];
        $locatedTierPrice = $this->tierPriceLocator->locate($tierPriceRule->getRanges(), $cartItem->getQuantity());

        if (!$locatedTierPrice instanceof ProductTierPriceRangeInterface) {
            return false;
        }

        $price = $this->calculateRangePrice($locatedTierPrice, $subject, $context);

        return !is_numeric($price) || $price === 0 ? false : $price;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateRangePrice(
        ProductTierPriceRangeInterface $range,
        TierPriceAwareInterface $subject,
        array $context
    ) {
        $price = 0;
        $realItemPrice = 0;

        if ($subject instanceof PurchasableInterface) {
            $realItemPrice = $this->productPriceCalculator->getPrice($subject, $context, true);
        }

        $pricingBehaviour = $range->getPricingBehaviour();

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
