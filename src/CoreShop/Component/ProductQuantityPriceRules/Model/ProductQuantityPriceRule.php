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

use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductQuantityPriceRule implements ProductQuantityPriceRuleInterface
{
    use TimestampableTrait;
    use SetValuesTrait;
    use ToggleableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $product;

    /**
     * @var bool
     */
    protected $inherit = false;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $calculationBehaviour;

    /**
     * @var ArrayCollection|RuleInterface[]
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConditions()
    {
        return !$this->conditions->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCondition(ConditionInterface $condition)
    {
        return $this->conditions->contains($condition);
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(ConditionInterface $condition)
    {
        if (!$this->hasCondition($condition)) {
            $this->conditions->add($condition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCondition(ConditionInterface $condition)
    {
        $this->conditions->removeElement($condition);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasActions()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction(ActionInterface $action)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use hasRange() instead.');
    }

    /**
     * {@inheritdoc}
     */
    public function addAction(ActionInterface $range)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use addRange() instead.');
    }

    /**
     * {@inheritdoc}
     */
    public function removeAction(ActionInterface $range)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use addRange() instead.');
    }

    /**
     * {@inheritdoc}
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRanges()
    {
        return !$this->ranges->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasRange(QuantityRangeInterface $range)
    {
        return $this->ranges->contains($range);
    }

    /**
     * {@inheritdoc}
     */
    public function addRange(QuantityRangeInterface $range)
    {
        if (!$this->hasRange($range)) {
            $range->setRule($this);
            $this->ranges->add($range);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRange(QuantityRangeInterface $range)
    {
        $range->setRule(null);
        $this->ranges->removeElement($range);
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculationBehaviour()
    {
        return $this->calculationBehaviour;
    }

    /**
     * {@inheritdoc}
     */
    public function setCalculationBehaviour($calculationBehaviour)
    {
        $this->calculationBehaviour = $calculationBehaviour;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
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
