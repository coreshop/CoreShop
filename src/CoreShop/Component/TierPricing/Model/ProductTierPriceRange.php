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

use CoreShop\Component\Resource\Model\AbstractResource;

class ProductTierPriceRange extends AbstractResource implements ProductTierPriceRangeInterface
{
    const PRICING_BEHAVIOUR_FIXED = 'fixed';
    const PRICING_BEHAVIOUR_AMOUNT_DISCOUNT = 'amount_discount';
    const PRICING_BEHAVIOUR_AMOUNT_INCREASE = 'amount_increase';
    const PRICING_BEHAVIOUR_PERCENTAGE_DISCOUNT = 'percentage_discount';
    const PRICING_BEHAVIOUR_PERCENTAGE_INCREASE = 'percentage_increase';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $rangeFrom;

    /**
     * @var int
     */
    protected $rangeTo;

    /**
     * @var string
     */
    protected $pricingBehaviour;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var float
     */
    protected $percentage;

    /***
     * @var int
     */
    protected $pseudoPrice;

    /**
     * @var bool
     */
    protected $highlighted;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getRangeFrom()
    {
        return $this->rangeFrom;
    }

    /**
     * @inheritdoc
     */
    public function setRangeFrom(int $rangeFrom)
    {
        $this->rangeFrom = $rangeFrom;
    }

    /**
     * @inheritdoc
     */
    public function getRangeTo()
    {
        return $this->rangeTo;
    }

    /**
     * @inheritdoc
     */
    public function setRangeTo(int $rangeTo)
    {
        $this->rangeTo = $rangeTo;
    }

    /**
     * @inheritdoc
     */
    public function getPricingBehaviour()
    {
        return $this->pricingBehaviour;
    }

    /**
     * @param string $pricingBehaviour
     */
    public function setPricingBehaviour(string $pricingBehaviour)
    {
        $this->pricingBehaviour = $pricingBehaviour;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @inheritdoc
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @inheritdoc
     */
    public function setPercentage(float $percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * @inheritdoc
     */
    public function getPseudoPrice()
    {
        return $this->pseudoPrice;
    }

    /**
     * @inheritdoc
     */
    public function hasPseudoPrice()
    {
        return $this->pseudoPrice !== 0;
    }

    /**
     * @inheritdoc
     */
    public function setPseudoPrice(int $pseudoPrice)
    {
        $this->pseudoPrice = $pseudoPrice;
    }

    /**
     * @inheritdoc
     */
    public function getHighlighted()
    {
        return $this->highlighted;
    }

    /**
     * @inheritdoc
     */
    public function isHighlighted()
    {
        return $this->highlighted === true;
    }

    /**
     * @inheritdoc
     */
    public function setHighlighted(bool $highlighted)
    {
        $this->highlighted = $highlighted;
    }
}
