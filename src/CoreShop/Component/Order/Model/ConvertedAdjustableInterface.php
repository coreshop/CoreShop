<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Order\Model;

interface ConvertedAdjustableInterface
{
    /**
     * @return AdjustmentInterface[]
     */
    public function getConvertedAdjustments(?string $type = null);

    public function addConvertedAdjustment(AdjustmentInterface $adjustment);

    public function removeConvertedAdjustment(AdjustmentInterface $adjustment);

    public function getConvertedAdjustmentsTotal(?string $type = null, bool $withTax = true): int;

    public function removeConvertedAdjustments(?string $type = null);

    public function removeConvertedAdjustmentsRecursively(?string $type = null);

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateConvertedAdjustmentsTotal();
}
