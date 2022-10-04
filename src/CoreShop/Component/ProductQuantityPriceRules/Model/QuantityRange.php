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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Model;

use CoreShop\Component\Resource\Model\AbstractResource;

/**
 * @psalm-suppress MissingConstructor
 */
class QuantityRange extends AbstractResource implements QuantityRangeInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var float
     */
    protected $rangeStartingFrom;

    /**
     * @var string
     */
    protected $pricingBehaviour;

    /**
     * @var float
     */
    protected $percentage = 0;

    /**
     * @var bool
     */
    protected $highlighted = false;

    /**
     * @var ProductQuantityPriceRuleInterface|null
     */
    protected $rule;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getRangeStartingFrom()
    {
        return $this->rangeStartingFrom;
    }

    /**
     * @return void
     */
    public function setRangeStartingFrom(float $rangeStartingFrom)
    {
        $this->rangeStartingFrom = $rangeStartingFrom;
    }

    public function getPricingBehaviour()
    {
        return $this->pricingBehaviour;
    }

    /**
     * @return void
     */
    public function setPricingBehaviour(string $pricingBehaviour)
    {
        $this->pricingBehaviour = $pricingBehaviour;
    }

    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @return void
     */
    public function setPercentage(float $percentage)
    {
        $this->percentage = $percentage;
    }

    public function getHighlighted()
    {
        return $this->highlighted;
    }

    public function isHighlighted()
    {
        return $this->highlighted === true;
    }

    /**
     * @return void
     */
    public function setHighlighted(bool $highlighted)
    {
        $this->highlighted = $highlighted;
    }

    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return void
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }
}
