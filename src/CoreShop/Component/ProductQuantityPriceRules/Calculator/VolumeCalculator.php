<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
    protected ServiceRegistryInterface $actionRegistry;

    public function __construct(ServiceRegistryInterface $actionRegistry)
    {
        $this->actionRegistry = $actionRegistry;
    }

    public function calculateForQuantity(
        ProductQuantityPriceRuleInterface $quantityPriceRule,
        QuantityRangePriceAwareInterface $subject,
        float $quantity,
        int $originalPrice,
        array $context
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
        array $context
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
        foreach ($ranges as $index => $range) {
            if ($range->getRangeStartingFrom() > $quantity) {
                break;
            }

            $cheapestRangePrice = $range;
        }

        return $cheapestRangePrice;
    }
}
