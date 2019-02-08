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

namespace CoreShop\Component\ProductQuantityPriceRules\Detector;

use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

interface QuantityReferenceDetectorInterface
{
    /**
     * @param QuantityRangePriceAwareInterface $subject
     * @param array                            $context
     *
     * @return bool|ProductQuantityPriceRuleInterface
     */
    public function detectRule(QuantityRangePriceAwareInterface $subject, array $context);

    /**
     * @param QuantityRangePriceAwareInterface $subject
     * @param int                              $quantity
     * @param int                              $originalPrice
     * @param array                            $context
     *
     * @return bool|int
     */
    public function detectPerQuantityPrice(QuantityRangePriceAwareInterface $subject, int $quantity, int $originalPrice, array $context);

    /**
     * @param QuantityRangePriceAwareInterface $subject
     * @param int                              $originalPrice
     * @param array                            $context
     *
     * @return bool|int
     */
    public function detectPerItemPrice(QuantityRangePriceAwareInterface $subject, int $originalPrice, array $context);

    /**
     * @param QuantityRangePriceAwareInterface $subject
     * @param QuantityRangeInterface           $range
     * @param int                              $originalPrice
     * @param array                            $context
     *
     * @return mixed
     */
    public function detectPerItemInRangePrice(QuantityRangePriceAwareInterface $subject, QuantityRangeInterface $range, int $originalPrice, array $context);
}
