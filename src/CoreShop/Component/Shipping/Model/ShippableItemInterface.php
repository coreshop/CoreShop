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

namespace CoreShop\Component\Shipping\Model;

interface ShippableItemInterface
{
    /**
     * @param bool $withTax
     * @return int
     */
    public function getTotal(bool $withTax = true);

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setTotal(int $total, bool $withTax = true);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @return float
     */
    public function getDepth();

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @return mixed
     */
    public function getProduct();
}
