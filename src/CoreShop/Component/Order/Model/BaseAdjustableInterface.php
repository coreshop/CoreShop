<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

interface BaseAdjustableInterface
{
    /**
     * @param string|null $type
     *
     * @return AdjustmentInterface[]
     */
    public function getBaseAdjustments(string $type = null);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function addBaseAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function removeBaseAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param string|null $type
     *
     * @return int
     */
    public function getBaseAdjustmentsTotal(string $type = null);

    /**
     * @param string|null $type
     */
    public function removeBaseAdjustments(string $type = null);

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateBaseAdjustmentsTotal();
}
