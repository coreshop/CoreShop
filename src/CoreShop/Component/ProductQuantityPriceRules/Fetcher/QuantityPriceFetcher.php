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

namespace CoreShop\Component\ProductQuantityPriceRules\Fetcher;

use CoreShop\Component\ProductQuantityPriceRules\Calculator\CalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

class QuantityPriceFetcher
{
    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     */
    public function __construct(ServiceRegistryInterface $calculatorRegistry)
    {
        $this->calculatorRegistry = $calculatorRegistry;
    }

    /**
     * @param ProductQuantityPriceRuleInterface $rule
     * @param QuantityRangePriceAwareInterface  $subject
     * @param int                               $quantity
     * @param int                               $originalPrice
     * @param array                             $context
     *
     * @throws NoPriceFoundException
     *
     * @return int
     */
    public function fetchQuantityPrice(
        ProductQuantityPriceRuleInterface $rule,
        QuantityRangePriceAwareInterface $subject,
        int $quantity,
        int $originalPrice,
        array $context
    ) {
        /**
         * @var CalculatorInterface $service
         */
        $service = $this->calculatorRegistry->get($rule->getCalculationBehaviour());

        return $service->calculateForQuantity($rule, $subject, $quantity, $originalPrice, $context);
    }

    /**
     * @param QuantityRangeInterface           $range
     * @param QuantityRangePriceAwareInterface $subject
     * @param int                              $originalPrice
     * @param array                            $context
     *
     * @throws NoPriceFoundException
     *
     * @return int
     */
    public function fetchRangePrice(
        QuantityRangeInterface $range,
        QuantityRangePriceAwareInterface $subject,
        int $originalPrice,
        array $context
    ) {
        /**
         * @var CalculatorInterface $service
         */
        $service = $this->calculatorRegistry->get($range->getRule()->getCalculationBehaviour());

        return $service->calculateForRange($range, $subject, $originalPrice, $context);
    }
}
