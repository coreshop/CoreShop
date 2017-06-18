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

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface SaleItemInterface extends ProposalItemInterface, PimcoreModelInterface
{
    /**
     * @return bool
     */
    public function getIsGiftItem();

    /**
     * @param bool $isGiftItem
     */
    public function setIsGiftItem($isGiftItem);

    /**
     * @return SaleInterface
     */
    public function getSaleDocument();

    /**
     * @param $itemPrice
     * @param bool $withTax
     *
     * @return mixed
     */
    public function setItemPrice($itemPrice, $withTax = true);

    /**
     * @param $itemRetailPrice
     * @param bool $withTax
     *
     * @return mixed
     */
    public function setItemRetailPrice($itemRetailPrice, $withTax = true);

    /**
     * @param $wholesalePrice
     *
     * @return mixed
     */
    public function setItemWholesalePrice($wholesalePrice);

    /**
     * @param $itemTax
     *
     * @return mixed
     */
    public function setItemTax($itemTax);

    /**
     * @param $taxes
     *
     * @return mixed
     */
    public function setTaxes($taxes);

    /**
     * @param $total
     * @param bool $withTax
     *
     * @return mixed
     */
    public function setTotal($total, $withTax = true);

    /**
     * @param $totalTax
     *
     * @return mixed
     */
    public function setTotalTax($totalTax);

    /**
     * @param float $totalWeight
     */
    public function setTotalWeight($totalWeight);

    /**
     * @param float $itemWeight
     */
    public function setItemWeight($itemWeight);

    /**
     * @return mixed
     */
    public function getTaxes();

    /**
     * @param bool $withTax
     *
     * @return mixed
     */
    public function getBaseItemPrice($withTax = true);

    /**
     * @param $itemPrice
     * @param bool $withTax
     *
     * @return mixed
     */
    public function setBaseItemPrice($itemPrice, $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return mixed
     */
    public function getBaseItemRetailPrice($withTax = true);

    /**
     * @param $itemRetailPrice
     * @param bool $withTax
     *
     * @return mixed
     */
    public function setBaseItemRetailPrice($itemRetailPrice, $withTax = true);

    /**
     * @return float
     */
    public function getBaseItemTax();

    /**
     * @param $itemTax
     *
     * @return mixed
     */
    public function setBaseItemTax($itemTax);

    /**
     * @return mixed
     */
    public function getBaseTaxes();

    /**
     * @param $taxes
     *
     * @return mixed
     */
    public function setBaseTaxes($taxes);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getBaseTotal($withTax = true);

    /**
     * @param $total
     * @param bool $withTax
     *
     * @return mixed
     */
    public function setBaseTotal($total, $withTax = true);

    /**
     * @param $totalTax
     *
     * @return mixed
     */
    public function setBaseTotalTax($totalTax);
}
