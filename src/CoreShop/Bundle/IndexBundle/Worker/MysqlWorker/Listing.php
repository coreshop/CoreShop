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

namespace CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;

use CoreShop\Bundle\IndexBundle\Extension\MysqlIndexQueryExtensionInterface;
use CoreShop\Bundle\IndexBundle\Worker\AbstractListing;
use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker\Listing\Dao;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Condition\MatchCondition;
use CoreShop\Component\Index\Listing\ExtendedListingInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Listing\OrderAwareListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderInterface;
use CoreShop\Component\Index\Order\SimpleOrder;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

class Listing extends AbstractListing implements OrderAwareListingInterface, ExtendedListingInterface
{
    /**
     * @var null|PimcoreModelInterface[]
     */
    protected $objects = null;

    /**
     * @var null|int
     */
    protected $totalCount = null;

    /**
     * @var string
     */
    protected $variantMode = ListingInterface::VARIANT_MODE_INCLUDE;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var PimcoreModelInterface
     */
    protected $category;

    /**
     * @var Dao
     */
    protected $dao;

    /**
     * @var OrderInterface|string|null
     */
    protected $order;

    /**
     * @var string | array
     */
    protected $orderKey;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var ConditionInterface[]
     */
    protected $conditions = [];

    /**
     * @var OrderInterface[]
     */
    protected $orders = [];

    /**
     * @var ConditionInterface[]
     */
    protected $relationConditions = [];

    /**
     * @var ConditionInterface[][]
     */
    protected $queryConditions = [];

    /**
     * @var string[][]
     */
    protected $queryJoins = [];

    /**
     * @var MysqlWorker
     */
    protected $worker;

    /**
     * {@inheritdoc}
     */
    public function __construct(IndexInterface $index, WorkerInterface $worker, Connection $connection)
    {
        parent::__construct($index, $worker, $connection);

        $this->dao = new Dao($this, $connection);
    }

    /**
     * @return MysqlWorker
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjects()
    {
        if ($this->objects === null) {
            $this->load();
        }

        return $this->objects;
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(ConditionInterface $condition, $fieldName)
    {
        $this->objects = null;
        $this->conditions[$fieldName][] = $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function resetCondition($fieldName)
    {
        $this->objects = null;
        unset($this->conditions[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function addRelationCondition(ConditionInterface $condition, $fieldName)
    {
        $this->objects = null;
        $this->relationConditions[$fieldName][] = $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function resetConditions()
    {
        $this->conditions = [];
        $this->relationConditions = [];
        $this->queryConditions = [];
        $this->queryJoins = [];

        $this->objects = null;
    }

    /**
     * {@inheritdoc}
     */
    public function addQueryCondition(ConditionInterface $condition, $fieldName)
    {
        $this->objects = null;
        $this->queryConditions[$fieldName][] = $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function resetQueryCondition($fieldName)
    {
        $this->objects = null;
        unset($this->queryConditions[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($order)
    {
        if ($this->order instanceof SimpleOrder) {
            $this->order = new SimpleOrder($this->order->getKey(), $order);
        }
        $this->objects = null;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder(OrderInterface $order)
    {
        $this->objects = null;
        $this->orders[] = $order;
    }

    /**
     * {@inheritdoc}
     */
    public function resetOrder()
    {
        $this->orders = [];
        $this->order = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order instanceof SimpleOrder ? $this->order->getDirection() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderKey($orderKey)
    {
        $this->objects = null;
        $this->order = new SimpleOrder($orderKey, 'ASC');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderKey()
    {
        return $this->order instanceof SimpleOrder ? $this->order->getKey() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit)
    {
        if ($this->limit != $limit) {
            $this->objects = null;
        }
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffset($offset)
    {
        if ($this->offset != $offset) {
            $this->objects = null;
        }
        $this->offset = $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(PimcoreModelInterface $category)
    {
        $this->objects = null;
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariantMode($variantMode)
    {
        $this->objects = null;
        $this->variantMode = $variantMode;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantMode()
    {
        return $this->variantMode;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options = [])
    {
        $queryBuilder = $this->dao->createQueryBuilder();
        $this->addQueryFromConditions($queryBuilder);
        $this->addOrderBy($queryBuilder);
        $this->addJoins($queryBuilder);
        $queryBuilder->setMaxResults($this->getLimit());
        $queryBuilder->setFirstResult($this->getOffset());
        $objectRaws = $this->dao->load($queryBuilder);
        $this->totalCount = $this->dao->getLastRecordCount();
        $className = $this->index->getClass();
        $this->objects = [];
        foreach ($objectRaws as $raw) {
            $object = $this->loadElementById($raw['o_id']);

            if ($object instanceof Concrete) {
                if ($object->getClassName() === $className) {
                    $this->objects[] = $object;
                }
            }
        }

        return $this->objects;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadElementById($elementId)
    {
        return AbstractObject::getById($elementId);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        $excludedFieldName = $fieldName;
        if (!$fieldNameShouldBeExcluded) {
            $excludedFieldName = null;
        }

        $queryBuilder = $this->dao->createQueryBuilder();
        $this->addQueryFromConditions($queryBuilder, false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE);

        return $this->dao->loadGroupByValues($queryBuilder, $fieldName, $countValues);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        $excludedFieldName = $fieldName;
        if (!$fieldNameShouldBeExcluded) {
            $excludedFieldName = null;
        }

        $queryBuilder = $this->dao->createQueryBuilder();
        $this->addQueryFromConditions($queryBuilder, false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE);

        return $this->dao->loadGroupByRelationValues($queryBuilder, $fieldName, $countValues);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupByRelationValuesAndType(
        $fieldName,
        $type,
        $countValues = false,
        $fieldNameShouldBeExcluded = true
    ) {
        $excludedFieldName = $fieldName;
        if (!$fieldNameShouldBeExcluded) {
            $excludedFieldName = null;
        }

        $queryBuilder = $this->dao->createQueryBuilder();
        $this->addQueryFromConditions($queryBuilder, false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE);

        return $this->dao->loadGroupByRelationValuesAndType($queryBuilder, $fieldName, $type, $countValues);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        // not supported with mysql tables
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function buildSimilarityOrderBy($fields, $objectId)
    {
        return $this->dao->buildSimilarityOrderBy($fields, $objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return $this->worker->getTablename($this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryTableName()
    {
        return $this->worker->getLocalizedViewName($this->index, $this->getLocale());
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationTablename()
    {
        return $this->worker->getRelationTablename($this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function quote($value)
    {
        return $this->dao->quote($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function addQueryFromConditions(QueryBuilder $queryBuilder, $excludeConditions = false, $excludedFieldName = null, $variantMode = null)
    {
        if ($variantMode == null) {
            $variantMode = $this->getVariantMode();
        }

        $queryBuilder->where($this->worker->renderCondition(new MatchCondition('active', '1'), 'q'));

        if ($this->getCategory()) {
            $categoryCondition = ',' . $this->getCategory()->getId() . ',';
            $queryBuilder->andWhere($this->worker->renderCondition(new LikeCondition('parentCategoryIds', 'both', $categoryCondition), 'q'));
        }
        $extensions = $this->getWorker()->getExtensions($this->getIndex());

        foreach ($extensions as $extension) {
            if ($extension instanceof MysqlIndexQueryExtensionInterface) {
                $conditions = $extension->preConditionQuery($this->getIndex());
                foreach ($conditions as $cond) {
                    $queryBuilder->andWhere($this->worker->renderCondition($cond, 'q'));
                }
            }
        }

        //variant handling and userspecific conditions
        if ($variantMode == AbstractListing::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            if (!$excludeConditions) {
                $this->addUserSpecificConditions($queryBuilder, $excludedFieldName);
            }
        } else {
            if ($variantMode == AbstractListing::VARIANT_MODE_HIDE) {
                $queryBuilder->andWhere('q.o_type != \'variant\'');
            }
            if (!$excludeConditions) {
                $this->addUserSpecificConditions($queryBuilder, $excludedFieldName);
            }
        }

        if (is_array($this->queryConditions)) {
            $searchString = '';
            foreach ($this->queryConditions as $condition) {
                if ($condition instanceof ConditionInterface) {
                    $searchString .= '+' . $condition->getValues() . '+ ';
                }
            }
            //$condition .= ' AND '.$this->dao->buildFulltextSearchWhere(["name"], $searchString); //TODO: Load array("name") from any configuration (cause its also used by indexservice)
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function addUserSpecificConditions(QueryBuilder $queryBuilder, $excludedFieldName = null)
    {
        $relationalTableName = $this->worker->getRelationTablename($this->index);
        foreach ($this->relationConditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $cond = $this->worker->renderCondition($cond, 'q');
                    $queryBuilder->andWhere('q.o_id IN (SELECT DISTINCT src FROM ' . $relationalTableName . ' q WHERE ' . $cond . ')');
                }
            }
        }
        foreach ($this->conditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $queryBuilder->andWhere($this->worker->renderCondition($cond, 'q'));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function addOrderBy(QueryBuilder $queryBuilder)
    {
        if ($this->order instanceof SimpleOrder) {
            $queryBuilder->add('orderBy', $this->worker->renderOrder($this->order, 'q'));
        }

        foreach ($this->orders as $order) {
            $queryBuilder->add('orderBy', $this->worker->renderOrder($order, 'q'));
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function addJoins(QueryBuilder $queryBuilder)
    {
        foreach ($this->queryJoins as $table => $tableJoins) {
            $joinType = isset($tableJoins['type']) ? ' ' . $tableJoins['type'] : ' LEFT';
            if (empty($tableJoins['joinTableAlias'])) {
                continue;
            }
            $joinName = $tableJoins['joinTableAlias'];
            $objectKeyField = isset($tableJoins['objectKeyField']) ? $tableJoins['objectKeyField'] : 'o_id';

            $function = 'join';
            switch (strtolower($joinType)) {
                case 'inner':
                    $function = 'innerJoin';

                    break;
                case 'left':
                    $function = 'leftJoin';

                    break;
                case 'right':
                    $function = 'rightJoin';

                    break;
                default:
                    break;
            }
            //innerJoin($fromAlias, $join, $alias, $condition = null)
            $queryBuilder->$function($joinName, $table, $joinName, $objectKeyField . ' = q.o_id');
        }
        $extensions = $this->getWorker()->getExtensions($this->getIndex());

        foreach ($extensions as $extension) {
            if ($extension instanceof MysqlIndexQueryExtensionInterface) {
                $extension->addJoins($this->getIndex(), $queryBuilder);
            }
        }
    }

    /**
     * @return int|null
     */
    public function count()
    {
        if ($this->totalCount === null) {
            $queryBuilder = $this->dao->createQueryBuilder();
            $this->addQueryFromConditions($queryBuilder);
            $this->addJoins($queryBuilder);
            $this->totalCount = $this->dao->getCount($queryBuilder);
        }

        return $this->totalCount;
    }

    /**
     * @return PimcoreModelInterface|bool
     */
    public function current()
    {
        $this->getObjects();
        $var = current($this->objects);

        return $var;
    }

    /**
     * @inheritdoc
     */
    public function getItems(int $offset, int $itemCountPerPage)
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);

        return $this->getObjects();
    }

    /**
     * @return mixed
     */
    public function key()
    {
        $this->getObjects();
        $var = key($this->objects);

        return $var;
    }

    /**
     * @return PimcoreModelInterface|bool
     */
    public function next()
    {
        $this->getObjects();
        $var = next($this->objects);

        return $var;
    }

    public function rewind()
    {
        $this->getObjects();
        reset($this->objects);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $var = $this->current() !== false;

        return $var;
    }
}
