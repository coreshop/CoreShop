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
 *
*/

namespace CoreShop\Component\Order\Model;

interface OrderInvoiceItemInterface extends OrderDocumentItemInterface
{
    /**
     * @param bool $withTax
     * @return float
     */
    public function getTotal($withTax = true);

    /**
     * @param float $total
     * @param boolean $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return float
     */
    public function getTotalTax();

    /**
     * @param float $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @return mixed
     */
    public function getTaxes();

    /**
     * @param mixed $taxes
     */
    public function setTaxes($taxes);
}