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

namespace CoreShop\Component\ProductQuantityPriceRules\Calculator;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use Doctrine\Common\Collections\Collection;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;

class VolumeCalculator implements CalculatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $actionRegistry;

    /**
     * @param ServiceRegistryInterface $actionRegistry
     */
    public function __construct(ServiceRegistryInterface $actionRegistry)
    {
        $this->actionRegistry = $actionRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateForQuantity(
        ProductQuantityPriceRuleInterface $quantityPriceRule,
        QuantityRangePriceAwareInterface $subject,
        float $quantity,
        int $originalPrice,
        array $context
    ) {
        $locatedRange = $this->locate($quantityPriceRule->getRanges(), $quantity);

        if (!$locatedRange instanceof QuantityRangeInterface) {
            throw new NoPriceFoundException(__CLASS__);
        }

        $price = $this->calculateRangePrice($locatedRange, $subject, $originalPrice, $context);

        if (!is_numeric($price) || $price === 0) {
            throw new NoPriceFoundException(__CLASS__);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateForRange(
        QuantityRangeInterface $range,
        QuantityRangePriceAwareInterface $subject,
        int $originalPrice,
        array $context
    ) {
        $price = $this->calculateRangePrice($range, $subject, $originalPrice, $context);

        if (!is_numeric($price) || $price === 0) {
            throw new NoPriceFoundException(__CLASS__);
        }

        return $price;
    }

    /**
     * @param QuantityRangeInterface           $range
     * @param QuantityRangePriceAwareInterface $subject
     * @param int                              $originalPrice
     * @param array                            $context
     *
     * @return int
     */
    public function calculateRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $subject, int $originalPrice, array $context)
    {
        $pricingBehaviour = $range->getPricingBehaviour();

        /**
         * @var ProductQuantityPriceRuleActionInterface $service
         */
        $service = $this->actionRegistry->get($pricingBehaviour);

        return $service->calculate($range, $subject, $originalPrice, $context);
    }

    /**
     * @param Collection $ranges
     * @param float      $quantity
     *
     * @return QuantityRangeInterface|null
     */
    protected function locate(Collection $ranges, float $quantity)
    {
        if ($ranges->isEmpty()) {
            return null;
        }

        $cheapestRangePrice = null;
        /** @var QuantityRangeInterface $range */
        foreach ($ranges as $index => $range) {
            if ($range->getRangeStartingFrom() > $quantity) {
                break;
            }

            $cheapestRangePrice = $range;
        }

        return $cheapestRangePrice;
    }
}
