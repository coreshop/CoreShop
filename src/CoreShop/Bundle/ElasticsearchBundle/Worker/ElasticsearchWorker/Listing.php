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

namespace CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker;

use CoreShop\Bundle\ElasticsearchBundle\Extension\ElasticsearchIndexQueryExtensionInterface;
use CoreShop\Bundle\ElasticsearchBundle\Worker\AbstractListing;
use CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker;
use CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker\Listing\Dao;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\MatchCondition;
use CoreShop\Component\Index\Condition\NotMatchCondition;
use CoreShop\Component\Index\Listing\ExtendedListingInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Listing\OrderAwareListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderInterface;
use CoreShop\Component\Index\Order\SimpleOrder;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

class Listing extends AbstractListing implements OrderAwareListingInterface, ExtendedListingInterface
{
    protected ?array $objects = null;

    protected ?int $totalCount = null;

    protected string $variantMode = ListingInterface::VARIANT_MODE_INCLUDE;

    protected ?int $limit = null;

    protected ?int $offset = null;

    protected Dao $dao;

    /**
     * @var OrderInterface|string|null
     */
    protected $order;

    /**
     * @var string|array
     */
    protected $orderKey;

    protected bool $enabled = true;

    /**
     * @var array<string, ConditionInterface[]>
     */
    protected array $conditions = [];

    /**
     * @var OrderInterface[]
     */
    protected array $orders = [];

    /**
     * @var array<string, ConditionInterface[]>
     */
    protected array $relationConditions = [];

    /**
     * @var array<string, ConditionInterface[]>
     */
    protected array $queryConditions = [];

    /**
     * @var string[][]
     */
    protected array $queryJoins = [];

    protected WorkerInterface $worker;

    public function __construct(IndexInterface $index, WorkerInterface $worker, Connection $connection)
    {
        parent::__construct($index, $worker);

        if (!$this->worker instanceof ElasticsearchWorker) {
            throw new \InvalidArgumentException('Worker needs to be a ElasticsearchWorker');
        }

        $this->dao = new Dao($this, $connection);
    }

    /**
     * @return ElasticsearchWorker
     */
    public function getWorker()
    {
        /**
         * @var ElasticsearchWorker $worker
         */
        $worker = $this->worker;

        return $worker;
    }

    public function getObjects()
    {
        if ($this->objects === null) {
            $this->load();
        }

        return $this->objects;
    }

    public function addCondition(ConditionInterface $condition, $fieldName)
    {
        $this->objects = null;

        if (!array_key_exists($fieldName, $this->conditions)) {
            $this->conditions[$fieldName] = [];
        }

        $this->conditions[$fieldName][] = $condition;
    }

    public function resetCondition($fieldName)
    {
        $this->objects = null;
        unset($this->conditions[$fieldName]);
    }

    public function addRelationCondition(ConditionInterface $condition, $fieldName)
    {
        if (!array_key_exists($fieldName, $this->relationConditions)) {
            $this->relationConditions[$fieldName] = [];
        }

        $this->objects = null;
        $this->relationConditions[$fieldName][] = $condition;
    }

    public function resetConditions()
    {
        $this->conditions = [];
        $this->relationConditions = [];
        $this->queryConditions = [];
        $this->queryJoins = [];

        $this->objects = null;
    }

    public function addQueryCondition(ConditionInterface $condition, $fieldName)
    {
        if (!array_key_exists($fieldName, $this->queryConditions)) {
            $this->queryConditions[$fieldName] = [];
        }

        $this->objects = null;
        $this->queryConditions[$fieldName][] = $condition;
    }

    public function resetQueryCondition($fieldName)
    {
        $this->objects = null;
        unset($this->queryConditions[$fieldName]);
    }

    public function setOrder($order)
    {
        if ($this->order instanceof SimpleOrder) {
            $this->order = new SimpleOrder($this->order->getKey(), $order);
        }
        $this->objects = null;
    }

    public function addOrder(OrderInterface $order)
    {
        $this->objects = null;
        $this->orders[] = $order;
    }

    public function resetOrder()
    {
        $this->orders = [];
        $this->order = null;
    }

    public function getOrder()
    {
        return $this->order instanceof SimpleOrder ? $this->order->getDirection() : null;
    }

    public function setOrderKey($orderKey)
    {
        $this->objects = null;
        $this->order = new SimpleOrder($orderKey, 'ASC');
    }

    public function getOrderKey()
    {
        return $this->order instanceof SimpleOrder ? $this->order->getKey() : null;
    }

    public function setLimit($limit)
    {
        if ($this->limit != $limit) {
            $this->objects = null;
        }
        $this->limit = $limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setOffset($offset)
    {
        if ($this->offset != $offset) {
            $this->objects = null;
        }
        $this->offset = $offset;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function setVariantMode($variantMode)
    {
        $this->objects = null;
        $this->variantMode = $variantMode;
    }

    public function getVariantMode()
    {
        return $this->variantMode;
    }

    public function load(array $options = [])
    {
        $result = $this->sendRequest($this->getQuery());

        $objectRaws = [];

        if ($result['hits']) {
            $this->totalCount = $result['hits']['total']['value'];

            foreach ($result['hits']['hits'] as $hit) {
                $objectRaws[] = $hit['_id'];
            }
        }

        // load elements
        $className = $this->index->getClass();

        $this->objects = [];
        foreach ($objectRaws as $raw) {
            $object = $this->loadElementById($raw);

            if ($object instanceof Concrete) {
                if ($object->getClassName() === $className) {
                    $this->objects[] = $object;
                }
            }
        }

        return $this->objects;
    }

    protected function loadElementById($elementId)
    {
        return AbstractObject::getById($elementId);
    }

    public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        $excludedFieldName = $fieldName;

        if (!$fieldNameShouldBeExcluded) {
            $excludedFieldName = null;
        }

        $filters = $this->addQueryFromConditions(false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE);

        return $this->dao->loadGroupByValues($filters, $fieldName, $countValues);
    }

    public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        $excludedFieldName = $fieldName;
        if (!$fieldNameShouldBeExcluded) {
            $excludedFieldName = null;
        }

        $filters = $this->addQueryFromConditions(false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE);

        return $this->dao->loadGroupByRelationValues($filters, $fieldName, $countValues);
    }

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

        $filters = $this->addQueryFromConditions(false, $excludedFieldName, AbstractListing::VARIANT_MODE_INCLUDE);

        return $this->dao->loadGroupByValues($filters, $fieldName, $countValues);
    }

    public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true)
    {
        // not supported with mysql tables
        return [];
    }

    public function buildSimilarityOrderBy(array $fields, int $objectId): string
    {
        return $this->dao->buildSimilarityOrderBy($fields, $objectId);
    }

    public function getTableName()
    {
        return $this->getWorker()->getTablename($this->index->getName());
    }

    public function getQueryTableName()
    {
        return $this->getWorker()->getLocalizedViewName($this->index->getName(), $this->getLocale());
    }

    public function getRelationTablename()
    {
        return $this->getWorker()->getRelationTablename($this->index->getName());
    }

    public function quote($value)
    {
        return $this->dao->quote($value);
    }

    protected function addQueryFromConditions($excludeConditions = false, $excludedFieldName = null, $variantMode = null): array
    {
        if ($variantMode == null) {
            $variantMode = $this->getVariantMode();
        }

        $filters[] = $this->getWorker()->renderCondition(new MatchCondition('active', 'true'));

        $extensions = $this->getWorker()->getExtensions($this->getIndex());

        foreach ($extensions as $extension) {
            if ($extension instanceof ElasticsearchIndexQueryExtensionInterface) {
                $conditions = $extension->preConditionQuery($this->getIndex());
                foreach ($conditions as $cond) {
                    $filters[] = $this->getWorker()->renderCondition($cond);
                }
            }
        }

        //variant handling and userspecific conditions
        if ($variantMode == AbstractListing::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            if (!$excludeConditions) {
                $filters = array_merge($filters, $this->addUserSpecificConditions($excludedFieldName));
            }
        } else {
            if ($variantMode == AbstractListing::VARIANT_MODE_HIDE) {
                $filters[] = $this->getWorker()->renderCondition(new NotMatchCondition('o_type', 'variant'));
            }
            if (!$excludeConditions) {
                $filters = array_merge($filters, $this->addUserSpecificConditions($excludedFieldName));
            }
        }

        return $filters;
    }

    protected function addUserSpecificConditions($excludedFieldName = null):array
    {
        $renderedConditions = [];

        foreach ($this->relationConditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $renderedConditions[] = $this->getWorker()->renderCondition($cond);
                }
            }
        }

        foreach ($this->conditions as $fieldName => $condArray) {
            if ($fieldName !== $excludedFieldName && is_array($condArray)) {
                foreach ($condArray as $cond) {
                    $renderedConditions[] = $this->getWorker()->renderCondition($cond);
                }
            }
        }

        return $renderedConditions;
    }

    protected function addOrderBy(QueryBuilder $queryBuilder)
    {
        if ($this->order instanceof SimpleOrder) {
            $queryBuilder->add('orderBy', $this->getWorker()->renderOrder($this->order, 'q'));
        }

        foreach ($this->orders as $order) {
            $queryBuilder->add('orderBy', $this->getWorker()->renderOrder($order, 'q'));
        }
    }

    public function addJoins(QueryBuilder $queryBuilder)
    {
        foreach ($this->queryJoins as $table => $tableJoins) {
            $joinType = isset($tableJoins['type']) ? ' ' . $tableJoins['type'] : ' LEFT';
            if (empty($tableJoins['joinTableAlias'])) {
                continue;
            }
            $joinName = $tableJoins['joinTableAlias'];
            $objectKeyField = $tableJoins['objectKeyField'] ?? 'o_id';

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
            if ($extension instanceof ElasticsearchIndexQueryExtensionInterface) {
                $extension->addJoins($this->getIndex(), $queryBuilder);
            }
        }
    }

    public function count(): int
    {
        if ($this->totalCount === null) {
            /*$queryBuilder = $this->dao->createQueryBuilder();
            $this->addQueryFromConditions($queryBuilder);
            $this->addJoins($queryBuilder);
            $this->totalCount = $this->dao->getCount($queryBuilder);*/
            $this->totalCount = 0;
        }

        return $this->totalCount;
    }

    public function current(): \CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface|bool
    {
        $this->getObjects();

        return current($this->objects);
    }

    /**
     * @inheritdoc
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);

        return $this->getObjects();
    }

    public function key(): mixed
    {
        $this->getObjects();

        return key($this->objects);
    }

    public function next(): void
    {
        $this->getObjects();

        next($this->objects);
    }

    public function rewind(): void
    {
        $this->getObjects();
        reset($this->objects);
    }

    public function valid(): bool
    {
        return $this->current() !== false;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function sendRequest($params)
    {
        $esClient = $this->getWorker()->getElasticsearchClient();

        $result = $esClient->search($params);

        return $result;
    }

    /**
     * @return array|string
     */
    protected function getQuery()
    {
        //user specific filters
        $filters = $this->addQueryFromConditions();

        $params = [];
        $params['index'] = $this->getTableName();
        $params['type'] = "coreshop";
        $params['body']['_source'] = false;
        $params['body']['query']['bool']['filter'] = $filters;

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
}
