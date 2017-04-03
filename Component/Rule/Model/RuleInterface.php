<?php

namespace CoreShop\Component\Rule\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface RuleInterface extends ResourceInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return Collection|ConditionInterface[]
     */
    public function getConditions();

    /**
     * @return bool
     */
    public function hasConditions();

    /**
     * @param ConditionInterface $conditions
     *
     * @return bool
     */
    public function hasCondition(ConditionInterface $conditions);

    /**
     * @param ConditionInterface $conditions
     */
    public function addCondition(ConditionInterface $conditions);

    /**
     * @param ConditionInterface $conditions
     */
    public function removeCondition(ConditionInterface $conditions);

    /**
     * @return Collection|ActionInterface[]
     */
    public function getActions();

    /**
     * @return bool
     */
    public function hasActions();

    /**
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function hasAction(ActionInterface $action);

    /**
     * @param ActionInterface $action
     */
    public function addAction(ActionInterface $action);

    /**
     * @param ActionInterface $action
     */
    public function removeAction(ActionInterface $action);
}