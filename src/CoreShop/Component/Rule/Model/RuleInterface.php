<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Rule\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use Doctrine\Common\Collections\Collection;

interface RuleInterface extends ResourceInterface, TimestampableInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
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
