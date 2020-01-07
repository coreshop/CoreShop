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

use CoreShop\Component\Resource\Model\AbstractResource;

class QuantityRange extends AbstractResource implements QuantityRangeInterface
{
    /**
     * @var int
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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRangeStartingFrom()
    {
        return $this->rangeStartingFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function setRangeStartingFrom(float $rangeStartingFrom)
    {
        $this->rangeStartingFrom = $rangeStartingFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function getPricingBehaviour()
    {
        return $this->pricingBehaviour;
    }

    /**
     * {@inheritdoc}
     */
    public function setPricingBehaviour(string $pricingBehaviour)
    {
        $this->pricingBehaviour = $pricingBehaviour;
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * {@inheritdoc}
     */
    public function setPercentage(float $percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * {@inheritdoc}
     */
    public function getHighlighted()
    {
        return $this->highlighted;
    }

    /**
     * {@inheritdoc}
     */
    public function isHighlighted()
    {
        return $this->highlighted === true;
    }

    /**
     * {@inheritdoc}
     */
    public function setHighlighted(bool $highlighted)
    {
        $this->highlighted = $highlighted;
    }

    /**
     * {@inheritdoc}
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * {@inheritdoc}
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }

    public function __clone()
    {
        if ($this->id === null) {
            return;
        }

        $this->rule = null;
        //$this->id = null;
    }
}
