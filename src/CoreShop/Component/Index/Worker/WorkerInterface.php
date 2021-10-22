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
     */
    public function createOrUpdateIndexStructures(IndexInterface $index);

    /**
     * deletes necessary index structuers (like database tables).
     */
    public function deleteIndexStructures(IndexInterface $index);

    /**
     * deletes given element from index.
     */
    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object);

    /**
     * updates given element in index.
     */
    public function updateIndex(IndexInterface $index, IndexableInterface $object);

    /**
     * @return IndexExtensionInterface[]
     */
    public function getExtensions(IndexInterface $index);

    /**
     * returns product list implementation valid and configured for this worker/tenant.
     *
     *
     * @return ListingInterface
     */
    public function getList(IndexInterface $index);

    /**
     * Renders the condition to fit the service.
     *
     * @param string             $prefix
     *
     * @return mixed
     */
    public function renderCondition(ConditionInterface $condition, $prefix = null);

    /**
     * Renders field type for the service.
     *
     *
     * @return mixed
     */
    public function renderFieldType(string $type);

    /**
     * @return FilterGroupHelperInterface
     */
    public function getFilterGroupHelper();
}
