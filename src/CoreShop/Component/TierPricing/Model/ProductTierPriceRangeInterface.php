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
use CoreShop\Component\Currency\Model\CurrencyInterface;

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
     * @return string
     */
    public function getPricingBehaviour();

    /**
     * @param string $pricingBehaviour
     */
    public function setPricingBehaviour(string $pricingBehaviour);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount(int $amount);

    /**
     * @return CurrencyInterface|null
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency = null);

    /**
     * @return float
     */
    public function getPercentage();

    /**
     * @param float $percentage
     */
    public function setPercentage(float $percentage);

    /**
     * @return int
     */
    public function getPseudoPrice();

    /**
     * @return bool
     */
    public function hasPseudoPrice();

    /**
     * @param int $pseudoPrice
     */
    public function setPseudoPrice(int $pseudoPrice);

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
