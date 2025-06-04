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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Rule\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait RuleTrait
{
    use TimestampableTrait;
    use SetValuesTrait;
    use ToggleableTrait;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Collection|ConditionInterface[]
     */
    protected $conditions;

    /**
     * @var Collection|ActionInterface[]
     */
    protected $actions;

    public function __construct(
        ) {
        $this->initializeRuleCollections();
    }

    protected function initializeRuleCollections()
    {
        $this->conditions = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }

    abstract public function getId(): ?int;

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
        return $this->actions;
    }

    public function hasActions()
    {
        return !$this->actions->isEmpty();
    }

    public function hasAction(ActionInterface $action)
    {
        return $this->actions->contains($action);
    }

    public function addAction(ActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            $this->actions->add($action);
        }
    }

    public function removeAction(ActionInterface $action)
    {
        $this->actions->removeElement($action);
    }
}
