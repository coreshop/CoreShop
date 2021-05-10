<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\ProductQuantityPriceRules\Fetcher;

use CoreShop\Component\ProductQuantityPriceRules\Calculator\CalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

class QuantityPriceFetcher
{
    private ServiceRegistryInterface $calculatorRegistry;

    public function __construct(ServiceRegistryInterface $calculatorRegistry)
    {
        $this->calculatorRegistry = $calculatorRegistry;
    }

    public function fetchQuantityPrice(
        ProductQuantityPriceRuleInterface $rule,
        QuantityRangePriceAwareInterface $subject,
        float $quantity,
        int $originalPrice,
        array $context
    ): int {
        /**
         * @var CalculatorInterface $service
         */
        $service = $this->calculatorRegistry->get($rule->getCalculationBehaviour());

        return $service->calculateForQuantity($rule, $subject, $quantity, $originalPrice, $context);
    }

    public function fetchRangePrice(
        QuantityRangeInterface $range,
        QuantityRangePriceAwareInterface $subject,
        int $originalPrice,
        array $context
    ): int {
        /**
         * @var CalculatorInterface $service
         */
        $service = $this->calculatorRegistry->get($range->getRule()->getCalculationBehaviour());

        return $service->calculateForRange($range, $subject, $originalPrice, $context);
    }
}
