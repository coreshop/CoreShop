<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product;

use CoreShop\Model\Category;
use CoreShop\Model\Index;
use CoreShop\Model\Product;

abstract class Listing implements \Zend_Paginator_Adapter_Interface, \Zend_Paginator_AdapterAggregate, \Iterator
{
    const ORDERKEY_PRICE = "orderkey_price";

    /**
     * Variant mode defines how to consider variants in product list results
     * - does not consider variants in search results
     */
    const VARIANT_MODE_HIDE = "hide";

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results and returns objects and variants
     */
    const VARIANT_MODE_INCLUDE = "include";

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results but only returns corresponding objects in search results
     */
    const VARIANT_MODE_INCLUDE_PARENT_OBJECT = "include_parent_object";

    /**
     * @var Index|null
     */
    public $index = null;

    /**
     * Listing constructor.
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    /**
     * Returns all products valid for this search
     *
     * @return \CoreShop\Model\Product[]
     */
    public abstract function getProducts();


    /**
     * Adds filter condition to product list
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results
     *
     * @param string $condition
     * @param string $fieldname
     */
    public abstract function addCondition($condition, $fieldname = "");


    /**
     * Adds query condition to product list for fulltext search
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results
     *
     * @param $condition
     * @param string $fieldname
     */
    public abstract function addQueryCondition($condition, $fieldname = "");

    /**
     * Reset filter condition for fieldname
     *
     * @param $fieldname
     * @return mixed
     */
    public abstract function resetCondition($fieldname);

    /**
     * Reset query condition for fieldname
     *
     * @param $fieldname
     * @return mixed
     */
    public abstract function resetQueryCondition($fieldname);


    /**
     * Adds relation condition to product list
     *
     * @param string $fieldname
     * @param string $condition
     */
    public abstract function addRelationCondition($fieldname, $condition);


    /**
     * Resets all conditions of product list
     */
    public abstract function resetConditions();


    /**
     * Adds price condition to product list
     *
     * @param null|float $from
     * @param null|float $to
     */
    public abstract function addPriceCondition($from = null, $to = null);

    /**
     * sets order direction
     *
     * @param $order
     * @return void
     */
    public abstract function setOrder($order);

    /**
     * gets order direction
     *
     * @return string
     */
    public abstract function getOrder();


    /**
     * sets order key
     *
     * @param $orderKey string | array  - either single field name, or array of field names or array of arrays (field name, direction)
     * @return void
     */
    public abstract function setOrderKey($orderKey);

    /**
     * @return string | array
     */
    public abstract function getOrderKey();

    /**
     * @param $limit int
     * @return void
     */
    public abstract function setLimit($limit);

    /**
     * @return int
     */
    public abstract function getLimit();

    /**
     * @param $offset int
     * @return void
     */
    public abstract function setOffset($offset);

    /**
     * @return int
     */
    public abstract function getOffset();

    /**
     * @param $category
     * @return void
     */
    public abstract function setCategory(Category $category);

    /**
     * @return \CoreShop\Model\PriceRule\Condition\Category
     */
    public abstract function getCategory();

    /**
     * @param $variantMode
     * @return void
     */
    public abstract function setVariantMode($variantMode);

    /**
     * @return string
     */
    public abstract function getVariantMode();

    /**
     * loads search results from index and returns them
     *
     * @return Product[]
     */
    public abstract function load();

    /**
     * @return Index|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param Index|null $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }
}