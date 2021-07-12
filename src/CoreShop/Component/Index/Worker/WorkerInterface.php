<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;

/**
 * @method renameIndexStructures(IndexInterface $index, string $oldName, string $newName);
 */
interface WorkerInterface
{
    /**
     * creates or updates necessary index structures (like database tables and so on).
     *
     * @param IndexInterface $index
     */
    public function createOrUpdateIndexStructures(IndexInterface $index);

    /**
     * deletes necessary index structuers (like database tables).
     *
     * @param IndexInterface $index
     *
     * @return mixed
     */
    public function deleteIndexStructures(IndexInterface $index);

    /**
     * deletes given element from index.
     *
     * @param IndexInterface     $index
     * @param IndexableInterface $object
     */
    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object);

    /**
     * updates given element in index.
     *
     * @param IndexInterface     $index
     * @param IndexableInterface $object
     */
    public function updateIndex(IndexInterface $index, IndexableInterface $object);

    /**
     * @param IndexInterface $index
     *
     * @return IndexExtensionInterface[]
     */
    public function getExtensions(IndexInterface $index);

    /**
     * returns product list implementation valid and configured for this worker/tenant.
     *
     * @param IndexInterface $index
     *
     * @return ListingInterface
     */
    public function getList(IndexInterface $index);

    /**
     * Renders the condition to fit the service.
     *
     * @param ConditionInterface $condition
     * @param string             $prefix
     *
     * @return mixed
     */
    public function renderCondition(ConditionInterface $condition, $prefix = null);

    /**
     * Renders field type for the service.
     *
     * @param string $type
     *
     * @return mixed
     */
    public function renderFieldType(string $type);

    /**
     * @return FilterGroupHelperInterface
     */
    public function getFilterGroupHelper();
}
