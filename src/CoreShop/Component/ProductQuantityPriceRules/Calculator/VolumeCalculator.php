<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Calculator;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Doctrine\Common\Collections\Collection;

class VolumeCalculator implements CalculatorInterface
{
    public function __construct(
        protected ServiceRegistryInterface $actionRegistry,
    ) {
    }

    public function calculateForQuantity(
        ProductQuantityPriceRuleInterface $quantityPriceRule,
        QuantityRangePriceAwareInterface $subject,
        float $quantity,
        int $originalPrice,
        array $context,
    ): int {
        $locatedRange = $this->locate($quantityPriceRule->getRanges(), $quantity);

        if (!$locatedRange instanceof QuantityRangeInterface) {
            throw new NoPriceFoundException(__CLASS__);
        }

        return $this->calculateRangePrice($locatedRange, $subject, $originalPrice, $context);
    }

    public function calculateForRange(
        QuantityRangeInterface $range,
        QuantityRangePriceAwareInterface $subject,
        int $originalPrice,
        array $context,
    ): int {
        return $this->calculateRangePrice($range, $subject, $originalPrice, $context);
    }

    public function calculateRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $subject, int $originalPrice, array $context): int
    {
        $pricingBehaviour = $range->getPricingBehaviour();

        /**
         * @var ProductQuantityPriceRuleActionInterface $service
         */
        $service = $this->actionRegistry->get($pricingBehaviour);

        return $service->calculate($range, $subject, $originalPrice, $context);
    }

    protected function locate(Collection $ranges, float $quantity): ?QuantityRangeInterface
    {
        if ($ranges->isEmpty()) {
            return null;
        }

        $cheapestRangePrice = null;
        /** @var QuantityRangeInterface $range */
        foreach ($ranges as $range) {
            if ($range->getRangeStartingFrom() > $quantity) {
                break;
            }

            $cheapestRangePrice = $range;
        }

        return $cheapestRangePrice;
    }
}
