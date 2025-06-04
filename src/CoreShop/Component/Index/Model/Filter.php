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

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class Filter extends AbstractResource implements FilterInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $resultsPerPage;

    /**
     * @var string
     */
    protected $orderDirection = 'ASC';

    /**
     * @var string
     */
    protected $orderKey = 'o_id';

    /**
     * @var IndexInterface
     */
    protected $index;

    /**
     * @var Collection|FilterConditionInterface[]
     */
    protected $preConditions;

    /**
     * @var Collection|FilterConditionInterface[]
     */
    protected $conditions;

    public function __construct(
        ) {
        $this->preConditions = new ArrayCollection();
        $this->conditions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }

    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    public function setOrderDirection($orderDirection)
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    public function getOrderKey()
    {
        return $this->orderKey;
    }

    public function setOrderKey($orderKey)
    {
        $this->orderKey = $orderKey;

        return $this;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;

        return $this;
    }

    public function getPreConditions()
    {
        return $this->preConditions;
    }

    public function hasPreConditions()
    {
        return !$this->preConditions->isEmpty();
    }

    public function addPreCondition(FilterConditionInterface $preCondition)
    {
        if (!$this->hasPreCondition($preCondition)) {
            $this->preConditions->add($preCondition);
        }
    }

    public function removePreCondition(FilterConditionInterface $preCondition)
    {
        if ($this->hasPreCondition($preCondition)) {
            $this->preConditions->removeElement($preCondition);
        }
    }

    public function hasPreCondition(FilterConditionInterface $preCondition)
    {
        return $this->preConditions->contains($preCondition);
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function hasConditions()
    {
        return !$this->conditions->isEmpty();
    }

    public function addCondition(FilterConditionInterface $condition)
    {
        if (!$this->hasCondition($condition)) {
            $this->conditions->add($condition);
        }
    }

    public function removeCondition(FilterConditionInterface $condition)
    {
        if ($this->hasCondition($condition)) {
            $this->conditions->removeElement($condition);
        }
    }

    public function hasCondition(FilterConditionInterface $condition)
    {
        return $this->conditions->contains($condition);
    }
}
