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

declare(strict_types=1);

namespace CoreShop\Component\ProductQuantityPriceRules\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface QuantityRangeInterface extends ResourceInterface
{
    /**
     * @return float
     */
    public function getRangeStartingFrom();

    public function setRangeStartingFrom(float $rangeStartingFrom);

    /**
     * @return string
     */
    public function getPricingBehaviour();

    public function setPricingBehaviour(string $pricingBehaviour);

    /**
     * @return float
     */
    public function getPercentage();

    public function setPercentage(float $percentage);

    /**
     * @return bool
     */
    public function getHighlighted();

    /**
     * @return bool
     */
    public function isHighlighted();

    public function setHighlighted(bool $highlighted);

    /**
     * @return ProductQuantityPriceRuleInterface
     */
    public function getRule();

    /**
     * @param ProductQuantityPriceRuleInterface|null $rule
     */
    public function setRule($rule);
}
