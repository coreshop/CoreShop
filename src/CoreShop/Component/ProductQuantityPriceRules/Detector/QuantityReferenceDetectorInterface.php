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

namespace CoreShop\Component\ProductQuantityPriceRules\Detector;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

interface QuantityReferenceDetectorInterface
{
    /**
     * @throws NoRuleFoundException
     */
    public function detectRule(QuantityRangePriceAwareInterface $subject, array $context): ProductQuantityPriceRuleInterface;

    /**
     * @throws NoPriceFoundException
     */
    public function detectQuantityPrice(QuantityRangePriceAwareInterface $subject, float $quantity, int $originalPrice, array $context): int;

    /**
     * @throws NoPriceFoundException
     */
    public function detectRangePrice(QuantityRangePriceAwareInterface $subject, QuantityRangeInterface $range, int $originalPrice, array $context): int;
}
