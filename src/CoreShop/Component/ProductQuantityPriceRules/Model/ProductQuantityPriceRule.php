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
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductQuantityPriceRule extends AbstractResource implements ProductQuantityPriceRuleInterface
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

    public function hasConditions(): bool
    {
        return !$this->conditions->isEmpty();
    }

    public function hasCondition(ConditionInterface $condition): bool
    {
        return $this->conditions->contains($condition);
    }

    public function addCondition(ConditionInterface $condition): void
    {
        if (!$this->hasCondition($condition)) {
            $this->conditions->add($condition);
        }
    }

    public function removeCondition(ConditionInterface $condition): void
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

    public function addAction(ActionInterface $action)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use addRange() instead.');
    }

    public function removeAction(ActionInterface $action)
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

    public function hasRange(QuantityRangeInterface $priceRange)
    {
        return $this->ranges->contains($priceRange);
    }

    public function addRange(QuantityRangeInterface $priceRange)
    {
        if (!$this->hasRange($priceRange)) {
            $priceRange->setRule($this);
            $this->ranges->add($priceRange);
        }
    }

    public function removeRange(QuantityRangeInterface $priceRange)
    {
        $priceRange->setRule(null);
        $this->ranges->removeElement($priceRange);
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
