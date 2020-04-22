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

namespace CoreShop\Component\Order\Model;

interface OrderInvoiceItemInterface extends OrderDocumentItemInterface
{
    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal(bool $withTax = true): int;

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setTotal(int $total, bool $withTax = true);

    /**
     * @return int
     */
    public function getTotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedTotal(bool $withTax = true): int;

    /**
     * @param int  $convertedTotal
     * @param bool $withTax
     */
    public function setConvertedTotal(int $convertedTotal, bool $withTax = true);
}
