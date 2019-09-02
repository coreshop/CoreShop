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

namespace CoreShop\Component\ProductQuantityPriceRules\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface QuantityRangeInterface extends ResourceInterface
{
    /**
     * @return float
     */
    public function getRangeStartingFrom();

    /**
     * @param float $rangeStartingFrom
     */
    public function setRangeStartingFrom(float $rangeStartingFrom);

    /**
     * @return string
     */
    public function getPricingBehaviour();

    /**
     * @param string $pricingBehaviour
     */
    public function setPricingBehaviour(string $pricingBehaviour);

    /**
     * @return float
     */
    public function getPercentage();

    /**
     * @param float $percentage
     */
    public function setPercentage(float $percentage);

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

    /**
     * @return ProductQuantityPriceRuleInterface
     */
    public function getRule();

    /**
     * @param ProductQuantityPriceRuleInterface $rule
     */
    public function setRule($rule);
}
