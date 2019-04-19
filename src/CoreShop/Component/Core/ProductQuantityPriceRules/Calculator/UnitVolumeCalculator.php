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

namespace CoreShop\Component\Core\ProductQuantityPriceRules\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Calculator\CalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Calculator\VolumeCalculator;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface as CoreQuantityRangeInterface;
use Doctrine\Common\Collections\Collection;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

class UnitVolumeCalculator implements CalculatorInterface
{
    /**
     * @var VolumeCalculator
     */
    protected $inner;

    /**
     * @var ServiceRegistryInterface
     */
    protected $actionRegistry;

    /**
     * @param CalculatorInterface      $inner
     * @param ServiceRegistryInterface $actionRegistry
     */
    public function __construct(CalculatorInterface $inner, ServiceRegistryInterface $actionRegistry)
    {
        $this->inner = $inner;
        $this->actionRegistry = $actionRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateForQuantity(
        ProductQuantityPriceRuleInterface $quantityPriceRule,
        QuantityRangePriceAwareInterface $subject,
        int $quantity,
        int $originalPrice,
        array $context
    ) {
        if (!isset($context['unitDefinition']) || !$context['unitDefinition'] instanceof ProductUnitDefinitionInterface) {
            return $this->inner->calculateForQuantity($quantityPriceRule, $subject, $quantity, $originalPrice, $context);
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
            $price = $price / (int) $subject->getItemQuantityFactor();
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
        return $this->inner->calculateForRange($range, $subject, $originalPrice, $context);
    }

    /**
     * @param Collection                     $ranges
     * @param int                            $quantity
     * @param ProductUnitDefinitionInterface $unitDefinition
     *
     * @return QuantityRangeInterface|null
     */
    protected function locate(Collection $ranges, int $quantity, ProductUnitDefinitionInterface $unitDefinition)
    {
        if ($ranges->isEmpty()) {
            return null;
        }

        $cheapestRangePrice = null;
        $unitFilteredRanges = array_filter($ranges->toArray(), function (CoreQuantityRangeInterface $range) use ($unitDefinition) {
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

            // if last range and quantity is greater: count!
            if ($index+1 === count($unitFilteredRanges) && $quantity > $range->getRangeTo()) {
                $cheapestRangePrice = $range;
                break;
            }

            if ($range->getRangeFrom() <= $quantity && $quantity <= $range->getRangeTo()) {
                $cheapestRangePrice = $range;
                break;
            }
        }

        return $cheapestRangePrice;
    }
}
