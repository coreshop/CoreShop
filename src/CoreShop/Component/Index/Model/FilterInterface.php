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

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface FilterInterface extends ResourceInterface, TimestampableInterface
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

    /**
     * @param FilterConditionInterface $preCondition
     */
    public function addPreCondition(FilterConditionInterface $preCondition);

    /**
     * @param FilterConditionInterface $preCondition
     */
    public function removePreCondition(FilterConditionInterface $preCondition);

    /**
     * @param FilterConditionInterface $preCondition
     *
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

    /**
     * @param FilterConditionInterface $condition
     */
    public function addCondition(FilterConditionInterface $condition);

    /**
     * @param FilterConditionInterface $condition
     */
    public function removeCondition(FilterConditionInterface $condition);

    /**
     * @param FilterConditionInterface $condition
     *
     * @return bool
     */
    public function hasCondition(FilterConditionInterface $condition);

    /**
     * @return IndexInterface
     */
    public function getIndex();

    /**
     * @param IndexInterface $index
     */
    public function setIndex(IndexInterface $index);
}
