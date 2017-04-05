<?php

namespace CoreShop\Component\Index\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\Listing\AbstractListing;

interface WorkerInterface {
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
     * @return mixed
     */
    public function deleteIndexStructures(IndexInterface $index);

   /**
    * deletes given element from index.
    *
    * @param IndexInterface $index
    * @param PimcoreModelInterface $object
    */
   public function deleteFromIndex(IndexInterface $index, PimcoreModelInterface $object);

    /**
     * updates given element in index.
     *
     * @param IndexInterface $index
     * @param PimcoreModelInterface $object
     */
    public function updateIndex(IndexInterface $index, PimcoreModelInterface $object);

    /**
     * returns product list implementation valid and configured for this worker/tenant.
     *
     * @param IndexInterface $index
     * @return AbstractListing
     */
    public function getList(IndexInterface $index);

    /**
     * Renders the condition to fit the service
     *
     * @param ConditionInterface $condition
     * @return mixed
     */
    public function renderCondition(ConditionInterface $condition);

    /**
     * Renders field type for the service
     *
     * @param $type
     * @return mixed
     */
    public function renderFieldType($type);
}