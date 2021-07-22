<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Detector;

use CoreShop\Component\ProductQuantityPriceRules\Fetcher\QuantityPriceFetcher;
use CoreShop\Component\ProductQuantityPriceRules\Fetcher\QuantityRuleFetcher;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

class QuantityReferenceDetector implements QuantityReferenceDetectorInterface
{
    /**
     * @var QuantityRuleFetcher
     */
    private $quantityRuleFetcher;

    /**
     * @var QuantityPriceFetcher
     */
    private $quantityPriceFetcher;

    /**
     * @param QuantityRuleFetcher  $quantityRuleFetcher
     * @param QuantityPriceFetcher $quantityPriceFetcher
     */
    public function __construct(QuantityRuleFetcher $quantityRuleFetcher, QuantityPriceFetcher $quantityPriceFetcher)
    {
        $this->quantityRuleFetcher = $quantityRuleFetcher;
        $this->quantityPriceFetcher = $quantityPriceFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function detectRule(QuantityRangePriceAwareInterface $subject, array $context)
    {
        return $this->quantityRuleFetcher->fetch($subject, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function detectQuantityPrice(QuantityRangePriceAwareInterface $subject, float $quantity, int $originalPrice, array $context)
    {
        $priceRule = $this->detectRule($subject, $context);

        return $this->quantityPriceFetcher->fetchQuantityPrice($priceRule, $subject, $quantity, $originalPrice, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function detectRangePrice(QuantityRangePriceAwareInterface $subject, QuantityRangeInterface $range, int $originalPrice, array $context)
    {
        return $this->quantityPriceFetcher->fetchRangePrice($range, $subject, $originalPrice, $context);
    }
}
