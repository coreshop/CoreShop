<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Listing;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Zend\Paginator\AdapterAggregateInterface;
use Zend\Paginator\Adapter\AdapterInterface;

interface ListingInterface extends AdapterInterface, AdapterAggregateInterface
{
    /**
     * Order Key Price.
     */
    const ORDERKEY_PRICE = 'orderkey_price';

    /**
     * Variant mode defines how to consider variants in product list results
     * - does not consider variants in search results.
     */
    const VARIANT_MODE_HIDE = 'hide';

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results and returns objects and variants.
     */
    const VARIANT_MODE_INCLUDE = 'include';

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results but only returns corresponding objects in search results.
     */
    const VARIANT_MODE_INCLUDE_PARENT_OBJECT = 'include_parent_object';

    /**
     * Listing constructor.
     *
     * @param IndexInterface  $index
     * @param WorkerInterface $worker
     */
    public function __construct(IndexInterface $index, WorkerInterface $worker);

    /**
     * Returns all products valid for this search.
     *
     * @return PimcoreModelInterface[]
     */
    public function getObjects();

    /**
     * Adds filter condition to product list
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results.
     *
     * @param ConditionInterface $condition
     * @param string             $fieldName
     */
    public function addCondition(ConditionInterface $condition, $fieldName);

    /**
     * Adds query condition to product list for fulltext search
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results.
     *
     * @param ConditionInterface $condition
     * @param string             $fieldName
     */
    public function addQueryCondition(ConditionInterface $condition, $fieldName);

    /**
     * Adds relation condition to product list.
     *
     * @param ConditionInterface $condition
     * @param string             $fieldName
     */
    public function addRelationCondition(ConditionInterface $condition, $fieldName);

    /**
     * Reset filter condition for fieldname.
     *
     * @param $fieldName
     */
    public function resetCondition($fieldName);

    /**
     * Reset query condition for fieldname.
     *
     * @param $fieldName
     */
    public function resetQueryCondition($fieldName);

    /**
     * Resets all conditions of product list.
     */
    public function resetConditions();

    /**
     * sets order direction.
     *
     * @param $order
     */
    public function setOrder($order);

    /**
     * gets order direction.
     *
     * @return string
     */
    public function getOrder();

    /**
     * sets order key.
     *
     * @param $orderKey string | array  - either single field name, or array of field names or array of arrays (field name, direction)
     */
    public function setOrderKey($orderKey);

    /**
     * @return string | array
     */
    public function getOrderKey();

    /**
     * @param $limit int
     */
    public function setLimit($limit);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param $offset int
     */
    public function setOffset($offset);

    /**
     * @return int
     */
    public function getOffset();

    /**
     * @param $category
     */
    public function setCategory(PimcoreModelInterface $category);

    /**
     * @return PimcoreModelInterface
     */
    public function getCategory();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @param $variantMode
     */
    public function setVariantMode($variantMode);

    /**
     * @return string
     */
    public function getVariantMode();

    /**
     * loads search results from index and returns them.
     *
     * @return PimcoreModelInterface[]
     */
    public function load();

    /**
     * loads group by values based on fieldname either from local variable if prepared or directly from product index.
     *
     * @param $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param      $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param      $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * returns order by statement for similarity calculations based on given fields and object ids
     * returns cosine similarity calculation.
     *
     * @param $fields
     * @param $objectId
     *
     * @return string
     */
    public function buildSimilarityOrderBy($fields, $objectId);

    /**
     * @return IndexInterface
     */
    public function getIndex();

    /**
     * @param IndexInterface $index
     */
    public function setIndex(IndexInterface $index);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);
}
