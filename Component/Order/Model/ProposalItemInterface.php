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

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProposalItemInterface extends ResourceInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param $product
     */
    public function setProduct($product);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param $quantity
     *
     * @return int
     */
    public function setQuantity($quantity);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getItemPrice($withTax = true);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getItemRetailPrice($withTax = true);

    /**
     * @return float
     */
    public function getItemWholesalePrice();

    /**
     * @return float
     */
    public function getItemTax();

    /**
     * @param boolean $applyDiscountToTaxValues
     * @return mixed
     */
    public function getTaxes($applyDiscountToTaxValues = true);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getTotal($withTax = true);

    /**
     * @return float
     */
    public function getTotalTax();

    /**
     * @return boolean
     */
    public function getIsGiftItem();

    /**
     * @return float
     */
    public function getItemWeight();

    /**
     * @return float
     */
    public function getTotalWeight();
}
