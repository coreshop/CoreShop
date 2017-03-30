<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Listing;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Condition;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Category;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Index;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Listing as AbstractListing;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop;
use Pimcore\Model\Object\AbstractObject;

/**
 * Class Elasticsearch
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Listing
 */
class Elasticsearch extends AbstractListing
{
    /**
     * @var null|Product[]
     */
    protected $products = null;

    /**
     * @var null|int
     */
    protected $totalCount = null;

    /**
     * @var string
     */
    protected $variantMode = AbstractListing::VARIANT_MODE_INCLUDE;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Shop
     */
    protected $shop;

    /**
     * @var
     */
    protected $order;

    /**
     * @var string | array
     */
    protected $orderKey;

    /**
     * @var bool
     */
    protected $orderByPrice = false;

    /**
     * @var string[]
     */
    protected $conditions = [];

    /**
     * @var string[]
     */
    protected $relationConditions = [];

    /**
     * @var string[][]
     */
    protected $queryConditions = [];

    /**
     * @var string[][]
     */
    protected $queryJoins = [];

    /**
     * @var \CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Elasticsearch
     */
    protected $worker;

    /**
     * @var array
     */
    protected $preparedGroupByValues = [];

    /**
     * @var array
     */
    protected $preparedGroupByValuesResults = [];

    /**
     * Mysql constructor.
     *
     * @param $index Index
     */
    public function __construct(Index $index)
    {
        parent::__construct($index);

        $this->worker = $this->getIndex()->getWorker();
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        if ($this->products === null) {
            $this->load();
        }

        return $this->products;
    }

    /**
     * @param Condition $condition
     * @param string $fieldName
     */
    public function addCondition(Condition $condition, $fieldName)
    {
        $this->products = null;
        $this->conditions[$fieldName][] = $condition;
    }

    /**
     * Reset conditions.
     *
     * @param $fieldName
     */
    public function resetCondition($fieldName)
    {
        $this->products = null;
        unset($this->conditions[$fieldName]);
    }

    /**
     * Add Relation Condition.
     *
     * @param Condition $condition
     * @param string $fieldName
     */
    public function addRelationCondition(Condition $condition, $fieldName)
    {
        $this->products = null;
        $this->relationConditions[$fieldName][] = $condition;
    }

    /**
     * resets all conditions of product list.
     */
    public function resetConditions()
    {
        $this->conditions = [];
        $this->relationConditions = [];
        $this->queryConditions = [];
        $this->queryJoins = [];

        $this->products = null;
    }

    /**
     * Adds query condition to product list for fulltext search
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results.
     *
     * @param Condition $condition
     * @param string $fieldName
     */
    public function addQueryCondition(Condition $condition, $fieldName)
    {
        $this->products = null;
        $this->queryConditions[$fieldName][] = $condition;
    }


    /**
     * Reset query condition for fieldname.
     *
     * @param $fieldName
     */
    public function resetQueryCondition($fieldName)
    {
        $this->products = null;
        unset($this->queryConditions[$fieldName]);
    }

    /**
     * set Order.
     *
     * @param $order
     */
    public function setOrder($order)
    {
        $this->products = null;
        $this->order = $order;
    }

    /**
     * get order.
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * set Order Key.
     *
     * @param $orderKey string | array  - either single field name, or array of field names or array of arrays (field name, direction)
     */
    public function setOrderKey($orderKey)
    {
        $this->products = null;
        if ($orderKey == AbstractListing::ORDERKEY_PRICE) {
            $this->orderByPrice = true;
        } else {
            $this->orderByPrice = false;
        }

        $this->orderKey = $orderKey;
    }

    /**
     * get Order Key.
     *
     * @return array|string
     */
    public function getOrderKey()
    {
        return $this->orderKey;
    }

    /**
     * set limit.
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        if ($this->limit != $limit) {
            $this->products = null;
        }
        $this->limit = $limit;
    }

    /**
     * get limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * set offset.
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        if ($this->offset != $offset) {
            $this->products = null;
        }
        $this->offset = $offset;
    }

    /**
     * get offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->products = null;
        $this->category = $category;
    }

    /**
     * get category.
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Shop $shop
     */
    public function setShop(Shop $shop)
    {
        $this->products = null;
        $this->shop = $shop;
    }

    /**
     * get shop.
     *
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * set variant mode.
     *
     * @param $variantMode
     */
    public function setVariantMode($variantMode)
    {
        $this->products = null;
        $this->variantMode = $variantMode;
    }

    /**
     * get variant mode.
     *
     * @return string
     */
    public function getVariantMode()
    {
        return $this->variantMode;
    }

    /**
     * load.
     *
     * @return array|\CoreShop\Bundle\CoreShopLegacyBundle\Model\Product[]|null
     *
     * @throws \Exception
     */
    public function load()
    {
        $result = $this->sendRequest($this->getQuery());

        $objectRaw = [];

        if ($result['hits']) {
            $this->totalCount = $result['hits']['total'];

            foreach ($result['hits']['hits'] as $hit) {
                $objectRaw[] = $hit['_id'];
            }
        }

        // load elements
        $this->products = [];
        $i = 0;

        foreach ($objectRaw as $raw) {
            $product = $this->loadElementById($raw);
            if ($product instanceof Product) {
                $this->products[] = $product;
                $i++;
            }
        }


        return $this->products;
    }

    /**
     * @return array|string
     */
    protected function getQuery()
    {
        $filters = $this->buildSystemConditions();

        //user specific filters
        $filters = array_merge($filters, $this->buildFilterConditions([]));

        //relation conditions
        $queryFilters = $this->buildQueryConditions([]);

        $params = [];
        $params['index'] = $this->worker->getIndex()->getName();
        $params['type'] = "coreshop";
        $params['body']['_source'] = false;
        $params['body']['size'] = $this->getLimit();
        $params['body']['from'] = $this->getOffset();
        $params['body']['query']['filtered']['query']['bool']['must'] = $queryFilters;
        $params['body']['query']['filtered']['filter']['bool']['must'] = $filters;

        if ($this->orderKey) {
            if (is_array($this->orderKey)) {
                foreach ($this->orderKey as $orderKey) {
                    $params['body']['sort'][] = [$orderKey[0] => ($orderKey[1] ?: "asc")];
                }
            } else {
                $params['body']['sort'][] = [$this->orderKey => ($this->order ?: "asc")];
            }
        }

        return $params;
    }

    /**
     * loads element by id.
     *
     * @param $elementId
     *
     * @return array|AbstractObject
     */
    protected function loadElementById($elementId)
    {
        return AbstractObject::getById($elementId);
    }

    /**
     * get group by values.
     *
     * @param $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws Exception
     */
    public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        return $this->doGetGroupByValues($fieldName, $countValues, $fieldNameShouldBeExcluded);
    }

    /**
     * get group by relation values.
     *
     * @param      $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws Exception
     */
    public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        return $this->doGetGroupByValues($fieldName, $countValues, $fieldNameShouldBeExcluded);
    }

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param      $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws Exception
     */
    public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        return $this->doGetGroupByValues($fieldName, $countValues, $fieldNameShouldBeExcluded);
    }


    /**
     * checks if group by values are loaded and returns them
     *
     * @param $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded
     * @return array
     */
    protected function doGetGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        $this->doLoadGroupByValues();

        $results = $this->preparedGroupByValuesResults[$fieldName];
        if ($results) {
            if ($countValues) {
                return $results;
            } else {
                $resultsWithoutCounts = [];
                foreach ($results as $result) {
                    $resultsWithoutCounts[] = $result['value'];
                }
                return $resultsWithoutCounts;
            }
        } else {
            return [];
        }
    }

    /**
     * loads all prepared group by values
     *   1 - get general filter (= filter of fields don't need to be considered in group by values or where fieldnameShouldBeExcluded set to false)
     *   2 - for each group by value create a own aggregation section with all other group by filters added
     *
     * @throws \Exception
     */
    protected function doLoadGroupByValues()
    {
        // create general filters and queries
        $filters = $this->buildSystemConditions();

        //user specific filters
        $filters = array_merge($filters, $this->buildFilterConditions([]));

        //relation conditions
        $queryFilters = $this->buildQueryConditions([]);

        $columns = $this->worker->getColumnsConfiguration();
        $aggregations = [];

        foreach ($columns as $column) {
            $aggregations[$column->getName()] = [
                "terms" => [
                    "field" => $column->getName(),
                    "size" => 0,
                    "order" => ["_term" => "asc"]
                ]
            ];
        }

        if (count($aggregations) > 0) {
            $params = [];
            $params['index'] = $this->worker->getIndex()->getName();
            $params['type'] = "coreshop";
            $params['search_type'] = "count";
            $params['body']['_source'] = false;
            $params['body']['size'] = $this->getLimit();
            $params['body']['from'] = $this->getOffset();
            $params['body']['aggs'] = $aggregations;
            $params['body']['query']['filtered']['query']['bool']['must'] = $queryFilters;
            $params['body']['query']['filtered']['filter']['bool']['must'] = $filters;


            // send request
            $result = $this->sendRequest($params);


            if ($result['aggregations']) {
                foreach ($result['aggregations'] as $fieldName => $aggregation) {
                    if ($aggregation['buckets']) {
                        $buckets = $aggregation['buckets'];
                    } else {
                        $buckets = $aggregation[$fieldName]['buckets'];
                    }

                    $groupByValueResult = [];
                    if ($buckets) {
                        foreach ($buckets as $bucket) {
                            $groupByValueResult[] = ['value' => $bucket['key'], 'count' => $bucket['doc_count']];
                        }
                    }

                    $this->preparedGroupByValuesResults[$fieldName] = $groupByValueResult;
                }
            }
        } else {
            $this->preparedGroupByValuesResults = [];
        }


        $this->preparedGroupByValuesLoaded = true;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function sendRequest($params)
    {
        $esClient = $this->worker->getElasticsearchClient();

        $result = $esClient->search($params);

        return $result;
    }

    /**
     * build system conditions.
     *
     * @return array
     */
    protected function buildSystemConditions()
    {
        $filters = [];

        $filters[] = [
            'term' => ['active' => true]
        ];

        if ($this->getCategory()) {
            $filters[] = [
                'term' => ['parentCategoryIds' => $this->getCategory()->getId()]
            ];
        }

        if ($this->getShop()) {
            $filters[] = [
                'term' => ['shops' => $this->getShop()->getId()]
            ];
        }

        return $filters;
    }

    /**
     * @param $excludedFieldName
     * @return array
     */
    protected function buildFilterConditions($excludedFieldName)
    {
        $filters = [];

        foreach ($this->conditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $filters[] = $this->worker->renderCondition($cond);
                }
            }
        }

        return $filters;
    }

    /**
     * @param $excludedFieldName
     * @return array
     */
    protected function buildQueryConditions($excludedFieldName)
    {
        $filters = [];

        if (is_array($this->queryConditions)) {
            foreach ($this->queryConditions as $queryCondition) {
                if ($queryCondition instanceof Condition) {
                    $filters[] = ['match' => [$queryCondition->getFieldName() => $queryCondition->getValues()]];
                }
            }
        }

        return $filters;
    }

    /**
     * build user specific conditions.
     *
     * @param null $excludedFieldName
     *
     * @return string
     */
    protected function buildUserSpecificConditions($excludedFieldName = null)
    {
        $renderedConditions = [];

        foreach ($this->relationConditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $renderedConditions[] = $this->worker->renderCondition($cond);
                }
            }
        }

        foreach ($this->conditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $renderedConditions[] = $this->worker->renderCondition($cond);
                }
            }
        }

        return $renderedConditions;
    }

    /**
     * build order by.
     *
     * @return null|string
     */
    protected function buildOrderBy()
    {
        if (!empty($this->orderKey) && $this->orderKey !== AbstractListing::ORDERKEY_PRICE) {
            $orderKeys = $this->orderKey;
            if (!is_array($orderKeys)) {
                $orderKeys = [$orderKeys];
            }

            $directionOrderKeys = [];
            foreach ($orderKeys as $key) {
                if (is_array($key)) {
                    $directionOrderKeys[] = $key;
                } else {
                    $directionOrderKeys[] = [$key, $this->order];
                }
            }

            $orderByStringArray = [];
            foreach ($directionOrderKeys as $keyDirection) {
                $key = $keyDirection[0];
                $direction = $keyDirection[1];

                if ($this->getVariantMode() == AbstractListing::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                    if (strtoupper($this->order) == 'DESC') {
                        $orderByStringArray[] = 'max('.$key.') '.$direction;
                    } else {
                        $orderByStringArray[] = 'min('.$key.') '.$direction;
                    }
                } else {
                    $orderByStringArray[] = $key.' '.$direction;
                }
            }

            return implode(',', $orderByStringArray);
        }

        return null;
    }

    /**
     * returns order by statement for similarity calculations based on given fields and object ids
     * returns cosine similarity calculation
     *
     * @param $fields
     * @param $objectId
     *
     * @return Product[]
     * @throws Exception
     */
    public function buildSimilarityOrderBy($fields, $objectId)
    {
        throw new Exception("not implemented");
    }

    /**
     *  -----------------------------------------------------------------------------------------
     *   Methods for Zend_Paginator_Adapter_Interface, Zend_Paginator_AdapterAggregate, Iterator
     *  -----------------------------------------------------------------------------------------.
     */

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *             </p>
     *             <p>
     *             The return value is cast to an integer.
     */
    public function count()
    {
        $this->getProducts();
        return $this->totalCount;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element.
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        $this->getProducts();
        $var = current($this->products);

        return $var;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param int $offset           Page offset
     * @param int $itemCountPerPage Number of items per page
     *
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);

        return $this->getProducts();
    }

    /**
     * Return a fully configured Paginator Adapter from this method.
     *
     * @return \Zend_Paginator_Adapter_Interface
     */
    public function getPaginatorAdapter()
    {
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element.
     *
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar scalar on success, integer
     *               0 on failure.
     */
    public function key()
    {
        $this->getProducts();
        $var = key($this->products);

        return $var;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element.
     *
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next()
    {
        $this->getProducts();
        $var = next($this->products);

        return $var;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element.
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        $this->getProducts();
        reset($this->products);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid.
     *
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     *              Returns true on success or false on failure.
     */
    public function valid()
    {
        $var = $this->current() !== false;

        return $var;
    }
}
