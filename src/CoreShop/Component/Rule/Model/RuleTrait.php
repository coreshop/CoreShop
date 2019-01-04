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
