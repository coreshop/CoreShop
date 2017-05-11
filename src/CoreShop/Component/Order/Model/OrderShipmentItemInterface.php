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

interface OrderShipmentItemInterface extends OrderDocumentItemInterface
{
    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getTotal($withTax = true);

    /**
     * @param float $total
     * @param bool  $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $weight
     */
    public function setWeight($weight);
}
