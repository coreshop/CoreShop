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

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface FilterInterface extends ResourceInterface, TimestampableInterface
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
     * @return int
     */
    public function getResultsPerPage();

    /**
     * @param int $resultsPerPage
     */
    public function setResultsPerPage($resultsPerPage);

    /**
     * @return string
     */
    public function getOrderDirection();

    /**
     * @param string $orderDirection
     */
    public function setOrderDirection($orderDirection);

    /**
     * @return string
     */
    public function getOrderKey();

    /**
     * @param string $orderKey
     */
    public function setOrderKey($orderKey);

    /**
     * @return Collection|FilterConditionInterface[]
     */
    public function getPreConditions();

    /**
     * @return bool
     */
    public function hasPreConditions();

    public function addPreCondition(FilterConditionInterface $preCondition);

    public function removePreCondition(FilterConditionInterface $preCondition);

    /**
     * @return bool
     */
    public function hasPreCondition(FilterConditionInterface $preCondition);

    /**
     * @return Collection|FilterConditionInterface[]
     */
    public function getConditions();

    /**
     * @return bool
     */
    public function hasConditions();

    public function addCondition(FilterConditionInterface $condition);

    public function removeCondition(FilterConditionInterface $condition);

    /**
     * @return bool
     */
    public function hasCondition(FilterConditionInterface $condition);

    /**
     * @return IndexInterface
     */
    public function getIndex();

    public function setIndex(IndexInterface $index);
}
