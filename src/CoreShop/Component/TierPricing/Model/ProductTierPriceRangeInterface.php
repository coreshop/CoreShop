<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\TierPricing\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProductTierPriceRangeInterface extends ResourceInterface
{
    /**
     * @return int
     */
    public function getRangeFrom();

    /**
     * @param int $rangeFrom
     */
    public function setRangeFrom(int $rangeFrom);

    /**
     * @return int
     */
    public function getRangeTo();

    /**
     * @param int $rangeTo
     */
    public function setRangeTo(int $rangeTo);

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @param int $price
     */
    public function setPrice(int $price);

    /**
     * @return float
     */
    public function getPercentageDiscount();

    /**
     * @param float $percentageDiscount
     */
    public function setPercentageDiscount(float $percentageDiscount);

    /**
     * @return bool
     */
    public function getHighlighted();

    /**
     * @return bool
     */
    public function isHighlighted();

    /**
     * @param bool $highlighted
     */
    public function setHighlighted(bool $highlighted);

}
