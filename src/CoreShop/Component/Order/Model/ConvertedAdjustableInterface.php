<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

interface ConvertedAdjustableInterface
{
    /**
     * @param string|null $type
     *
     * @return AdjustmentInterface[]
     */
    public function getConvertedAdjustments(string $type = null);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function addConvertedAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function removeConvertedAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param string|null $type
     * @param bool        $withTax
     *
     * @return int
     */
    public function getConvertedAdjustmentsTotal(string $type = null, bool $withTax = true): int;

    /**
     * @param string|null $type
     */
    public function removeConvertedAdjustments(string $type = null);

    /**
     * @param string|null $type
     */
    public function removeConvertedAdjustmentsRecursively(string $type = null);

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateConvertedAdjustmentsTotal();
}
