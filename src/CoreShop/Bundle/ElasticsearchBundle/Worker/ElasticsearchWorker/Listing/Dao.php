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

namespace CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker\Listing;

use CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class Dao
{
    private int $lastRecordCount = 0;

    /**
     * @var array
     */
    protected $preparedGroupByValues = [];

    /**
     * @var array
     */
    protected $preparedGroupByValuesResults = [];

    public function __construct(private ElasticsearchWorker\Listing $model, private Connection $database)
    {
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this->database);
    }

    /**
     * Load objects.
     *
     *
     * @return array
     */
    public function load(QueryBuilder $queryBuilder)
    {
        $queryBuilder->from($this->model->getQueryTableName(), 'q');
        if ($this->model->getVariantMode() == ListingInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            if (null !== $queryBuilder->getQueryPart('orderBy')) {
                $queryBuilder->select('DISTINCT q.o_virtualObjectId as o_id');
                $queryBuilder->addGroupBy('q.o_virtualObjectId');
            } else {
                $queryBuilder->select('DISTINCT q.o_virtualObjectId as o_id');
            }
        } else {
            $queryBuilder->select('DISTINCT q.o_id');
        }

        $resultSet = $this->database->fetchAllAssociative($queryBuilder->getSQL());

        $this->lastRecordCount = count($resultSet);

        return $resultSet;
    }

    /**
     * Load Group by values.
     *
     * @param string       $fieldName
     * @param bool         $countValues
     *
     * @return array
     */
    public function loadGroupByValues(array $filters, $fieldName, $countValues = false)
    {
        $this->doLoadGroupByValues($filters);

        $results = !empty($this->preparedGroupByValuesResults[$fieldName]) ? $this->preparedGroupByValuesResults[$fieldName] : [];

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

    protected function doLoadGroupByValues(array $filters)
    {
        $columns = $this->model->getIndex()->getColumns();
        //TODO aggregation for system columns
        $aggregations = [];

        foreach ($columns as $column) {
            $aggregations[$column->getName()] = [
                "terms" => [
                    "field" => $column->getName(),
                    "order" => ["_term" => "asc"]
                ]
            ];
        }

        $extensions = $this->model->getWorker()->getExtensions($this->model->getIndex());

        foreach ($extensions as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getSystemColumns() as $systemColumnName => $systemColumnType) {
                    $aggregations[$systemColumnName] = [
                        "terms" => [
                            "field" => $systemColumnName,
                            "order" => ["_term" => "asc"]
                        ]
                    ];
                }
            }
        }

        if (count($aggregations) > 0) {
            $params = [];
            $params['index'] = $this->model->getTableName();
            $params['type'] = "coreshop";
            $params['body']['_source'] = false;
            $params['body']['aggs'] = $aggregations;
            $params['body']['query']['bool']['filter'] = $filters;

            try {
                $queryResult = $this->model->getWorker()->getElasticsearchClient()->search($params);
            } catch (\Exception $exception) {
                dd($params, $exception);
            }

            if ($queryResult['aggregations']) {
                foreach ($queryResult['aggregations'] as $fieldName => $aggregation) {
                    $buckets = $aggregation['buckets'];

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
     * Load Grouo by Relation values.
     *
     * @param string       $fieldName
     * @param bool         $countValues
     *
     * @return array
     */
    public function loadGroupByRelationValues(array $filters, $fieldName, $countValues = false)
    {
        return $this->loadGroupByRelationValuesAndType($filters, $fieldName, null, $countValues);
    }

    /**
     * Load Grouo by Relation values and type.
     */
    public function loadGroupByRelationValuesAndType(array $filters, string $fieldName, ?string $type = null, bool $countValues = false): array
    {
        return [];
        $esClient = $this->model->getWorker()->getElasticsearchClient();

        if ($countValues) {
            $subQueryBuilder = new QueryBuilder($this->database);
            $subQueryBuilder->select($this->quoteIdentifier('o_id'));
            //$subQueryBuilder->from($this->model->getQueryTableName(), 'q');
            $subQueryBuilder->where($queryBuilder->getQueryPart('where'));

            if ($this->model->getVariantMode() === ListingInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                $queryBuilder->select($this->quoteIdentifier('dest') . ' AS ' . $this->quoteIdentifier('value') . ', count(DISTINCT src_virtualObjectId) AS ' . $this->quoteIdentifier('count'));
                $queryBuilder->where('fieldname = ' . $this->quote($fieldName));

                if (null !== $type) {
                    $queryBuilder->where('type = ' . $this->quote($type));
                }
            } else {
                $queryBuilder->select($this->quoteIdentifier('dest') . ' AS ' . $this->quoteIdentifier('value') . ', count(*) AS ' . $this->quoteIdentifier('count'));
                $queryBuilder->where('fieldname = ' . $this->quote($fieldName));

                if (null !== $type) {
                    $queryBuilder->where('type = ' . $this->quote($type));
                }
            }

            $queryBuilder->andWhere('src IN (' . $subQueryBuilder->getSQL() . ')');
            $queryBuilder->groupBy('dest');

            return $this->database->fetchAllAssociative($queryBuilder->getSQL());
        }
        dd('here2');
        $queryBuilder->select($this->quoteIdentifier('dest'));
        $queryBuilder->where('fieldname = ' . $this->quote($fieldName));

        if (null !== $type) {
            $queryBuilder->where('type = ' . $this->quote($type));
        }

        $subQueryBuilder = new QueryBuilder($this->database);
        $subQueryBuilder->select('o_id');
        $subQueryBuilder->from($this->model->getQueryTableName(), 'q');
        $subQueryBuilder->where($queryBuilder->getQueryPart('where'));
        $queryBuilder->andWhere('src IN (' . $subQueryBuilder->getSQL() . ')');
        $queryBuilder->groupBy('dest');

        $queryResult = $this->database->fetchAllAssociative($queryBuilder->getSQL());

        $result = [];

        foreach ($queryResult as $row) {
            if ($row['dest']) {
                $result[] = $row['dest'];
            }
        }

        return $result;
    }

    /**
     * Get Count.
     *
     *
     * @return int
     */
    public function getCount(QueryBuilder $queryBuilder)
    {
        $queryBuilder->from($this->model->getQueryTableName(), 'q');
        if ($this->model->getVariantMode() == ListingInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            $queryBuilder->select('count(DISTINCT o_virtualObjectId)');
        } else {
            $queryBuilder->select('count(*)');
        }
        $stmt = $this->database->executeQuery($queryBuilder->getSQL());

        return (int)$stmt->fetchOne();
    }

    /**
     * quote value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function quote($value)
    {
        return $this->database->quote($value);
    }

    /**
     * quote identifier.
     *
     * @param string $value
     *
     * @return string
     */
    public function quoteIdentifier($value)
    {
        return $this->database->quoteIdentifier($value);
    }

    /**
     * returns order by statement for similarity calculations based on given fields and object ids.
     */
    public function buildSimilarityOrderBy(array $fields, int $objectId): string
    {
        //TODO: similarity
        /*
        try {
            $fieldString = '';
            $maxFieldString = '';

            foreach ($fields as $field) {
                if ($field instanceof AbstractSimilarity) {
                    if (!empty($fieldString)) {
                        $fieldString .= ',';
                        $maxFieldString .= ',';
                    }


                    $fieldString .= $this->db->quoteIdentifier($field->getField());
                    $maxFieldString .= 'MAX('.$this->db->quoteIdentifier($field->getField()).') as '.$this->db->quoteIdentifier($field->getField());
                }
            }

            $query = 'SELECT '.$fieldString.' FROM '.$this->model->getQueryTableName().' a WHERE a.o_id = ?;';
            $objectValues = $this->db->fetchRow($query, $objectId);

            $query = 'SELECT '.$maxFieldString.' FROM '.$this->model->getQueryTableName().' a';
            $maxObjectValues = $this->db->fetchRow($query);

            if (!empty($objectValues)) {
                $subStatement = [];

                foreach ($fields as $field) {
                    if ($field instanceof AbstractSimilarity) {
                        if ($objectValues[$field->getField()]) {
                            $subStatement[] =
                                '(' .
                                $this->db->quoteIdentifier($field->getField()) . '/' . $maxObjectValues[$field->getField()] .
                                ' - ' .
                                $objectValues[$field->getField()] / $maxObjectValues[$field->getField()] .
                                ') * ' . $field->getWeight();
                        }
                    }
                }

                if (count($subStatement) > 0) {
                    $statement = 'ABS('.implode(' + ', $subStatement).')';

                    return $statement;
                }
            } else {
                throw new \Exception('Field array for given object id is empty');
            }
        } catch (\Exception $e) {
        }*/

        return '';
    }

    /**
     * returns where statement for fulltext search index.
     *
     * @param array  $fields
     * @param string $searchString
     *
     * @return string
     */
    public function buildFulltextSearchWhere($fields, $searchString)
    {
        $columnNames = [];

        foreach ($fields as $c) {
            $columnNames[] = $this->quoteIdentifier($c);
        }

        return 'MATCH (' . implode(',', $columnNames) . ') AGAINST (' . $this->quote($searchString) . ' IN BOOLEAN MODE)';
    }

    /**
     * get the record count for the last select query.
     */
    public function getLastRecordCount(): int
    {
        return $this->lastRecordCount;
    }
}
