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
