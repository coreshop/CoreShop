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

use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductQuantityPriceRule implements ProductQuantityPriceRuleInterface
{
    use TimestampableTrait;
    use SetValuesTrait;
    use ToggleableTrait;

    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var int|null
     */
    protected $product;

    /**
     * @var bool
     */
    protected $inherit = false;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $calculationBehaviour;

    /**
     * @var ArrayCollection|ConditionInterface[]
     */
    protected $conditions;

    /**
     * @var ArrayCollection|QuantityRangeInterface[]
     */
    protected $ranges;

    /**
     * @var int
     */
    protected $priority = 0;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
        $this->ranges = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function hasConditions()
    {
        return !$this->conditions->isEmpty();
    }

    public function hasCondition(ConditionInterface $condition)
    {
        return $this->conditions->contains($condition);
    }

    public function addCondition(ConditionInterface $condition)
    {
        if (!$this->hasCondition($condition)) {
            $this->conditions->add($condition);
        }
    }

    public function removeCondition(ConditionInterface $condition)
    {
        $this->conditions->removeElement($condition);
    }

    public function getActions()
    {
        return new ArrayCollection();
    }

    public function hasActions()
    {
        return false;
    }

    public function hasAction(ActionInterface $action)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use hasRange() instead.');
    }

    public function addAction(ActionInterface $range)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use addRange() instead.');
    }

    public function removeAction(ActionInterface $range)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use addRange() instead.');
    }

    public function getRanges()
    {
        return $this->ranges;
    }

    public function hasRanges()
    {
        return !$this->ranges->isEmpty();
    }

    public function hasRange(QuantityRangeInterface $range)
    {
        return $this->ranges->contains($range);
    }

    public function addRange(QuantityRangeInterface $range)
    {
        if (!$this->hasRange($range)) {
            $range->setRule($this);
            $this->ranges->add($range);
        }
    }

    public function removeRange(QuantityRangeInterface $range)
    {
        $range->setRule(null);
        $this->ranges->removeElement($range);
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    public function getCalculationBehaviour()
    {
        return $this->calculationBehaviour;
    }

    public function setCalculationBehaviour($calculationBehaviour)
    {
        $this->calculationBehaviour = $calculationBehaviour;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }

    public function __clone()
    {
        if ($this->id === null) {
            return;
        }

        $conditions = $this->getConditions();
        $ranges = $this->getRanges();

        $this->id = null;
        $this->product = null;
        $this->conditions = new ArrayCollection();
        $this->ranges = new ArrayCollection();

        if ($conditions instanceof Collection) {
            /** @var ConditionInterface $condition */
            foreach ($conditions as $condition) {
                $newCondition = clone $condition;
                $this->addCondition($newCondition);
            }
        }

        if ($ranges instanceof Collection) {
            /** @var QuantityRangeInterface $range */
            foreach ($ranges as $range) {
                $newRange = clone $range;
                $newRange->setRule($this);
                $this->addRange($newRange);
            }
        }
    }
}
