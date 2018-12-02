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

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var Collection|array
     */
    protected $preConditions;

    /**
     * @var Collection|array
     */
    protected $conditions;

    public function __construct()
    {
        $this->preConditions = new ArrayCollection();
        $this->conditions = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderDirection($orderDirection)
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderKey()
    {
        return $this->orderKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderKey($orderKey)
    {
        $this->orderKey = $orderKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreConditions()
    {
        return $this->preConditions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreConditions()
    {
        return !$this->preConditions->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addPreCondition(FilterConditionInterface $preCondition)
    {
        if (!$this->hasPreCondition($preCondition)) {
            $this->preConditions->add($preCondition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePreCondition(FilterConditionInterface $preCondition)
    {
        if ($this->hasPreCondition($preCondition)) {
            $this->preConditions->removeElement($preCondition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreCondition(FilterConditionInterface $preCondition)
    {
        return $this->preConditions->contains($preCondition);
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
    public function addCondition(FilterConditionInterface $condition)
    {
        if (!$this->hasCondition($condition)) {
            $this->conditions->add($condition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCondition(FilterConditionInterface $condition)
    {
        if ($this->hasCondition($condition)) {
            $this->conditions->removeElement($condition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCondition(FilterConditionInterface $condition)
    {
        return $this->conditions->contains($condition);
    }
}
