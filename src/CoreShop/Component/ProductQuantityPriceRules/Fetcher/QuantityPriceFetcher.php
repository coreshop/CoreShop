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

namespace CoreShop\Component\ProductQuantityPriceRules\Fetcher;

use CoreShop\Component\ProductQuantityPriceRules\Calculator\CalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

class QuantityPriceFetcher implements QuantityPriceFetcherInterface
{
    public function __construct(
        private ServiceRegistryInterface $calculatorRegistry,
    ) {
    }

    public function fetchQuantityPrice(
        ProductQuantityPriceRuleInterface $rule,
        QuantityRangePriceAwareInterface $subject,
        float $quantity,
        int $originalPrice,
        array $context,
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
        array $context,
    ): int {
        /**
         * @var CalculatorInterface $service
         */
        $service = $this->calculatorRegistry->get($range->getRule()->getCalculationBehaviour());

        return $service->calculateForRange($range, $subject, $originalPrice, $context);
    }
}
