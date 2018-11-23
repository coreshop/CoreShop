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

interface OrderInvoiceItemInterface extends OrderDocumentItemInterface
{
    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal($withTax = true);

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return int
     */
    public function getTotalTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseTotal($withTax = true);

    /**
     * @param int  $baseTotal
     * @param bool $withTax
     */
    public function setBaseTotal($baseTotal, $withTax = true);
}
