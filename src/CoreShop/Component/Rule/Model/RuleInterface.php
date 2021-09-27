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

    public function hasConditions(): bool;

    public function hasCondition(ConditionInterface $condition): bool;

    public function addCondition(ConditionInterface $condition): void;

    public function removeCondition(ConditionInterface $condition): void;

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
