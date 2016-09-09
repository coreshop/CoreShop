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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\Listing;

use CoreShop\Exception;
use CoreShop\IndexService\Condition;
use CoreShop\Model\Category;
use CoreShop\Model\Index;
use CoreShop\Model\Product;
use CoreShop\Model\Product\Listing as AbstractListing;
use CoreShop\Model\Shop;
use Pimcore\Model\Object\AbstractObject;

/**
 * Class Mysql
 * @package CoreShop\Model\Product\Listing
 */
class Mysql extends AbstractListing
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
     * @var Product\Listing\Mysql\Dao
     */
    protected $dao;

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
    protected $conditions = array();

    /**
     * @var string[]
     */
    protected $relationConditions = array();

    /**
     * @var string[][]
     */
    protected $queryConditions = array();

    /**
     * @var string[][]
     */
    protected $queryJoins = array();

    /**
     * @var \CoreShop\IndexService\Mysql
     */
    protected $worker;

    /**
     * Mysql constructor.
     *
     * @param $index Index
     */
    public function __construct(Index $index)
    {
        parent::__construct($index);

        $this->dao = new Product\Listing\Mysql\Dao($this);
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
        $this->relationConditions[$fieldName][] = $condition; //'`fieldname` = '.$this->quote($fieldName).' AND '.$condition;
    }

    /**
     * resets all conditions of product list.
     */
    public function resetConditions()
    {
        $this->conditions = array();
        $this->relationConditions = array();
        $this->queryConditions = array();
        $this->queryJoins = array();

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
     * @return array|\CoreShop\Model\Product[]|null
     *
     * @throws \Exception
     */
    public function load()
    {
        //TODO: Load with price filter?!

        $objectRaws = $this->dao->load($this->buildQueryFromConditions(), $this->buildOrderBy(), $this->getLimit(), $this->getOffset());
        $this->totalCount = $this->dao->getLastRecordCount();

        $this->products = array();
        foreach ($objectRaws as $raw) {
            $product = $this->loadElementById($raw['o_id']);
            if ($product) {
                $this->products[] = $product;
            }
        }

        return $this->products;
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
        $excludedFieldName = $fieldName;
        if (!$fieldNameShouldBeExcluded) {
            $excludedFieldName = null;
        }

        return $this->dao->loadGroupByValues($fieldName, $this->buildQueryFromConditions(false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE), $countValues);
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
        $excludedFieldName = $fieldName;
        if (!$fieldNameShouldBeExcluded) {
            $excludedFieldName = null;
        }

        return $this->dao->loadGroupByRelationValues($fieldName, $this->buildQueryFromConditions(false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE), $countValues);
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
        // not supported with mysql tables
        return [];
    }

    /**
     * build query from conditions.
     *
     * @param bool $excludeConditions
     * @param null $excludedFieldName
     * @param null $variantMode
     *
     * @return string
     */
    protected function buildQueryFromConditions($excludeConditions = false, $excludedFieldName = null, $variantMode = null)
    {
        if ($variantMode == null) {
            $variantMode = $this->getVariantMode();
        }

        $preCondition = 'active = 1';

        if ($this->getCategory()) {
            $preCondition .= " AND parentCategoryIds LIKE '%,".$this->getCategory()->getId().",%'";
        }

        if ($this->getShop()) {
            $preCondition .= " AND shops LIKE '%,".$this->getShop()->getId().",%'";
        }

        $condition = $preCondition;

        //variant handling and userspecific conditions

        if ($variantMode == AbstractListing::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            if (!$excludeConditions) {
                $userSpecific = $this->buildUserSpecificConditions($excludedFieldName);
                if ($userSpecific) {
                    $condition .= ' AND '.$userSpecific;
                }
            }
        } else {
            if ($variantMode == AbstractListing::VARIANT_MODE_HIDE) {
                $condition .= " AND o_type != 'variant'";
            }

            if (!$excludeConditions) {
                $userSpecific = $this->buildUserSpecificConditions($excludedFieldName);
                if ($userSpecific) {
                    $condition .= ' AND '.$userSpecific;
                }
            }
        }

        if ($this->queryConditions) {
            $searchString = '';

            foreach($this->queryConditions as $condition) {
                if($condition instanceof Condition) {
                    $searchString .= '+' . $condition->getValues() . '+ ';
                }
            }

            $condition .= ' AND '.$this->dao->buildFulltextSearchWhere(array("name"), $searchString); //TODO: Load array("name") from any configuration (cause its also used by indexservice)
        }

        return $condition;
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
        $relationalTableName = $this->worker->getRelationTablename();

        foreach ($this->relationConditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $cond = $this->worker->renderCondition($cond);

                    $renderedConditions[] = 'a.o_id IN (SELECT DISTINCT src FROM coreshop_product_index_relations WHERE '.$cond.')'; //TODO: Load tablename from any configuration (cause its also used by indexservice)
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

        return implode(" AND ", $renderedConditions);
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
                $orderKeys = array($orderKeys);
            }

            $directionOrderKeys = array();
            foreach ($orderKeys as $key) {
                if (is_array($key)) {
                    $directionOrderKeys[] = $key;
                } else {
                    $directionOrderKeys[] = array($key, $this->order);
                }
            }

            $orderByStringArray = array();
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
     */
    public function buildSimilarityOrderBy($fields, $objectId)
    {
        return $this->dao->buildSimilarityOrderBy($fields, $objectId);
    }

    /**
     * return tablename.
     *
     * @return string
     */
    public function getTableName()
    {
        return 'coreshop_index_mysql_'.$this->getIndex()->getName();
    }

    /**
     * get tablename for relations.
     *
     * @return string
     */
    public function getRelationTablename()
    {
        return 'coreshop_index_mysql_relations_'.$this->getIndex()->getName();
    }

    /**
     * quote value.
     *
     * @param $value
     *
     * @return mixed
     */
    public function quote($value)
    {
        return $this->dao->quote($value);
    }

    /**
     * get joins.
     *
     * @return string
     */
    public function getJoins()
    {
        if (empty($this->queryJoins)) {
            return '';
        }

        $query = '';

        foreach ($this->queryJoins as $table => $tableJoins) {
            $joinType = isset($tableJoins['type']) ? ' '.$tableJoins['type'] : ' LEFT';

            if (empty($tableJoins['joinTableAlias'])) {
                continue;
            }

            $joinName = $tableJoins['joinTableAlias'];
            $objectKeyField = isset($tableJoins['objectKeyField']) ? $tableJoins['objectKeyField'] : 'o_id';

            $query .= $joinType.' JOIN '.$table.' as '.$joinName.' on `'.$joinName.'`.'.$objectKeyField.' = a.o_id ';
        }

        return $query;
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
        if ($this->totalCount === null) {
            $this->totalCount = $this->dao->getCount($this->buildQueryFromConditions());
        }

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
