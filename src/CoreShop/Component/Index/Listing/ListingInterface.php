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

namespace CoreShop\Component\Index\Listing;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Paginator\PaginateListingInterface;

interface ListingInterface extends \Countable, PaginateListingInterface
{
    /**
     * Variant mode defines how to consider variants in product list results
     * - does not consider variants in search results.
     */
    public const VARIANT_MODE_HIDE = 'hide';

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results and returns objects and variants.
     */
    public const VARIANT_MODE_INCLUDE = 'include';

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results but only returns corresponding objects in search results.
     */
    public const VARIANT_MODE_INCLUDE_PARENT_OBJECT = 'include_parent_object';

    /**
     * Returns all products valid for this search.
     *
     * @return PimcoreModelInterface[]
     */
    public function getObjects();

    /**
     * @return PimcoreModelInterface[]
     */
    public function getItems($offset, $itemCountPerPage);

    /**
     * Adds filter condition to product list
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results.
     *
     * @param string             $fieldName
     */
    public function addCondition(ConditionInterface $condition, $fieldName);

    /**
     * Adds query condition to product list for fulltext search
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results.
     *
     * @param string             $fieldName
     */
    public function addQueryCondition(ConditionInterface $condition, $fieldName);

    /**
     * Adds relation condition to product list.
     *
     * @param string             $fieldName
     */
    public function addRelationCondition(ConditionInterface $condition, $fieldName);

    /**
     * Reset filter condition for fieldname.
     *
     * @param string $fieldName
     */
    public function resetCondition($fieldName);

    /**
     * Reset query condition for fieldname.
     *
     * @param string $fieldName
     */
    public function resetQueryCondition($fieldName);

    /**
     * Resets all conditions of product list.
     */
    public function resetConditions();

    /**
     * sets order direction.
     *
     * @param string $order
     */
    public function setOrder($order);

    /**
     * gets order direction.
     *
     * @return OrderInterface|string|null
     */
    public function getOrder();

    /**
     * sets order key.
     *
     * @param mixed $orderKey array or string - either single field name, or array of field names or array of arrays (field name, direction)
     */
    public function setOrderKey($orderKey);

    /**
     * @return mixed
     */
    public function getOrderKey();

    /**
     * @param int $limit
     */
    public function setLimit($limit);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $offset
     */
    public function setOffset($offset);

    /**
     * @return int
     */
    public function getOffset();

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
     * @param string $variantMode
     */
    public function setVariantMode($variantMode);

    /**
     * @return string
     */
    public function getVariantMode();

    /**
     * loads search results from index and returns them.
     *
     *
     * @return Concrete[]
     */
    public function load(array $options = []);

    /**
     * loads group by values based on fieldname either from local variable if prepared or directly from product index.
     *
     * @param string $fieldName
     * @param bool   $countValues
     * @param bool   $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param string $fieldName
     * @param bool   $countValues
     * @param bool   $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param string $fieldName
     * @param bool   $countValues
     * @param bool   $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * returns order by statement for similarity calculations based on given fields and object ids
     * returns cosine similarity calculation.
     */
    public function buildSimilarityOrderBy(array $fields, int $objectId): string;

    public function getIndex(): IndexInterface;

    public function setIndex(IndexInterface $index): void;

    public function getLocale(): ?string;

    public function setLocale(string $locale): void;
}
