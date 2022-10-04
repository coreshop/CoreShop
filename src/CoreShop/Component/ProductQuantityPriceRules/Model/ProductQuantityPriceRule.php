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
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductQuantityPriceRule extends AbstractResource implements ProductQuantityPriceRuleInterface, \Stringable
{
    use TimestampableTrait;
    use SetValuesTrait;
    use ToggleableTrait;

    /**
     * @var int
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

    public function __construct(
        ) {
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

    /**
     * @return void
     */
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

    /**
     * @return never
     */
    public function addAction(ActionInterface $action)
    {
        throw new \Exception('actions are not supported in quantity range price rules. use addRange() instead.');
    }

    /**
     * @return never
     */
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

    /**
     * @return void
     */
    public function addRange(QuantityRangeInterface $priceRange)
    {
        if (!$this->hasRange($priceRange)) {
            $priceRange->setRule($this);
            $this->ranges->add($priceRange);
        }
    }

    /**
     * @return void
     */
    public function removeRange(QuantityRangeInterface $priceRange)
    {
        $priceRange->setRule(null);
        $this->ranges->removeElement($priceRange);
    }

    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return static
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    public function getCalculationBehaviour()
    {
        return $this->calculationBehaviour;
    }

    /**
     * @return void
     */
    public function setCalculationBehaviour($calculationBehaviour)
    {
        $this->calculationBehaviour = $calculationBehaviour;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return static
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }
}
