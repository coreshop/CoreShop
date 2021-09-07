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

namespace CoreShop\Component\Order\Model;

interface AdjustableInterface
{
    /**
     * @param string|null $type
     *
     * @return AdjustmentInterface[]
     */
    public function getAdjustments(string $type = null);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function addAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function removeAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param null|string $type
     * @param bool        $withTax
     *
     * @return int
     */
    public function getAdjustmentsTotal(?string $type = null, bool $withTax = true): int;

    /**
     * @param string|null $type
     */
    public function removeAdjustments(string $type = null);

    /**
     * @param string|null $type
     */
    public function removeAdjustmentsRecursively(string $type = null);

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateAdjustmentsTotal();
}
