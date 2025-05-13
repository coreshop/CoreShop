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

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use Doctrine\Common\Collections\Collection;

interface RuleInterface extends ResourceInterface, TimestampableInterface, ToggleableInterface
{
    public function getId(): ?int;

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
     * @return bool
     */
    public function hasAction(ActionInterface $action);

    public function addAction(ActionInterface $action);

    public function removeAction(ActionInterface $action);
}
