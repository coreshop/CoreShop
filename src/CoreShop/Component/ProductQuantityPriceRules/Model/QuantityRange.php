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

use CoreShop\Component\Resource\Model\AbstractResource;

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

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getRangeStartingFrom()
    {
        return $this->rangeStartingFrom;
    }

    public function setRangeStartingFrom(float $rangeStartingFrom)
    {
        $this->rangeStartingFrom = $rangeStartingFrom;
    }

    public function getPricingBehaviour()
    {
        return $this->pricingBehaviour;
    }

    public function setPricingBehaviour(string $pricingBehaviour)
    {
        $this->pricingBehaviour = $pricingBehaviour;
    }

    public function getPercentage()
    {
        return $this->percentage;
    }

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

    public function setHighlighted(bool $highlighted)
    {
        $this->highlighted = $highlighted;
    }

    public function getRule()
    {
        return $this->rule;
    }

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
        $this->id = null;
    }
}
