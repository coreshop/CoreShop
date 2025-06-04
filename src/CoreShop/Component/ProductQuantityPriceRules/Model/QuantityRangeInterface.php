<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface QuantityRangeInterface extends ResourceInterface
{
    public function getId(): ?int;

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
