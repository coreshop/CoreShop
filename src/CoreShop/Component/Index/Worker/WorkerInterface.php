<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Index\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;

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
     * @return mixed
     */
    public function renderCondition(ConditionInterface $condition, array|string $params = []);

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

    public function renameIndexStructures(IndexInterface $index, string $oldName, string $newName): void;

    /**
     * Returns the list of supported field types.
     *
     * @return string[]
     */
    public function getSupportedFieldTypes(): array;
}
