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

    public function __construct()
    {
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

    /**
     * @return int
     */
    abstract public function getId();

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
