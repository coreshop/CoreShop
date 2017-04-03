<?php

namespace CoreShop\Component\Rule\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class AbstractRule implements RuleInterface
{
    use SetValuesTrait;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Collection|RuleInterface[]
     */
    protected $conditions;

    /**
     * @var Collection|ActionInterface[]
     */
    protected $actions;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
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
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActions()
    {
        return !$this->actions->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction(ActionInterface $action)
    {
        return $this->actions->contains($action);
    }

    /**
     * {@inheritdoc}
     */
    public function addAction(ActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            $this->actions->add($action);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAction(ActionInterface $action)
    {
        $this->actions->removeElement($action);
    }
}
