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

declare(strict_types=1);

namespace CoreShop\Component\Core\ProductQuantityPriceRules\Calculator;

use CoreShop\Component\Core\Model\QuantityRangeInterface as CoreQuantityRangeInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Calculator\CalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Calculator\VolumeCalculator;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Doctrine\Common\Collections\Collection;

class UnitVolumeCalculator implements CalculatorInterface
{
    protected VolumeCalculator $inner;
    protected ServiceRegistryInterface $actionRegistry;

    public function __construct(VolumeCalculator $inner, ServiceRegistryInterface $actionRegistry)
    {
        $this->inner = $inner;
        $this->actionRegistry = $actionRegistry;
    }

    public function calculateForQuantity(
        ProductQuantityPriceRuleInterface $quantityPriceRule,
        QuantityRangePriceAwareInterface $subject,
        float $quantity,
        int $originalPrice,
        array $context
    ): int {
        if (!isset($context['unitDefinition']) || !$context['unitDefinition'] instanceof ProductUnitDefinitionInterface) {
            return $this->inner->calculateForQuantity($quantityPriceRule, $subject, $quantity, $originalPrice,
                $context);
        }

        $locatedRange = $this->locate($quantityPriceRule->getRanges(), $quantity, $context['unitDefinition']);

        if (null === $locatedRange) {
            throw new NoPriceFoundException(__CLASS__);
        }

        $price = $this->inner->calculateRangePrice($locatedRange, $subject, $originalPrice, $context);

        if (!is_numeric($price) || $price === 0) {
            throw new NoPriceFoundException(__CLASS__);
        }

        if ($subject instanceof ProductInterface && is_numeric($subject->getItemQuantityFactor()) && $subject->getItemQuantityFactor() > 1) {
            $price = $price / (int)$subject->getItemQuantityFactor();
        }

        return $price;
    }

    public function calculateForRange(
        QuantityRangeInterface $range,
        QuantityRangePriceAwareInterface $subject,
        int $originalPrice,
        array $context
    ): int {
        return $this->inner->calculateForRange($range, $subject, $originalPrice, $context);
    }

    protected function locate(
        Collection $ranges,
        float $quantity,
        ProductUnitDefinitionInterface $unitDefinition
    ): ?QuantityRangeInterface {
        if ($ranges->isEmpty()) {
            return null;
        }

        $cheapestRangePrice = null;
        $unitFilteredRanges = array_filter($ranges->toArray(),
            function (CoreQuantityRangeInterface $range) use ($unitDefinition) {
                if (!$range->getUnitDefinition() instanceof ProductUnitDefinitionInterface) {
                    return false;
                }
                if ($range->getUnitDefinition()->getId() !== $unitDefinition->getId()) {
                    return false;
                }

                return true;
            });

        // reset array index
        $unitFilteredRanges = array_values($unitFilteredRanges);

        /** @var CoreQuantityRangeInterface $range */
        foreach ($unitFilteredRanges as $index => $range) {
            if ($range->getRangeStartingFrom() > $quantity) {
                break;
            }

            $cheapestRangePrice = $range;
        }

        return $cheapestRangePrice;
    }
}
