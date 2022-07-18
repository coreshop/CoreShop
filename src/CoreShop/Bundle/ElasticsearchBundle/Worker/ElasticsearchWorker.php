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

namespace CoreShop\Bundle\ElasticsearchBundle\Worker;

use CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker\TableIndex;
use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Extension\IndexColumnTypeConfigExtension;
use CoreShop\Component\Index\Extension\IndexRelationalColumnsExtensionInterface;
use CoreShop\Component\Index\Extension\IndexSystemColumnTypeConfigExtension;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderRendererInterface;
use CoreShop\Component\Index\Worker\FilterGroupHelperInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Pimcore\Db\Connection;
use Pimcore\Log\Simple;
use Pimcore\Tool;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticsearchWorker extends AbstractWorker
{
    protected Client $client;

    public function __construct(
        ServiceRegistryInterface $extensionsRegistry,
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry,
        FilterGroupHelperInterface $filterGroupHelper,
        ConditionRendererInterface $conditionRenderer,
        OrderRendererInterface $orderRenderer,
        protected Connection $database
    ) {
        parent::__construct(
            $extensionsRegistry,
            $getterServiceRegistry,
            $interpreterServiceRegistry,
            $filterGroupHelper,
            $conditionRenderer,
            $orderRenderer
        );

        $builder = ClientBuilder::create();
        $builder->setHosts(['http://elasticsearch:9200']);
        $this->client = $builder->build();
    }

    public function createOrUpdateIndexStructures(IndexInterface $index): void
    {
        $tableName = $this->getTablename($index->getName());

        try {
            $params = ['index' => $tableName];

            $this->client->indices()->delete($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }

        try {
            $result = $this->client->indices()->exists(['index' => $tableName]);
        } catch (\Exception $e) {
            $result = null;
            Simple::log('elastic-worker', (string)$e);
        }

        if (!$result) {
            $result = $this->client->indices()->create(
                [
                    'index' => $tableName,
                    'body' => [
                        'settings' => [
                            "number_of_shards" => 5,
                            "number_of_replicas" => 0
                        ]
                    ]
                ]
            );

            Simple::log('elastic-worker','Creating new Index. Name: ' . $tableName);

            if (!$result['acknowledged']) {
                throw new \Exception("Index creation failed. IndexName: " . $tableName);
            }
        } else {
            try {
                $this->client->indices()->delete(['index' => $tableName]);
            } catch (\Exception $e) {
                Simple::log('elastic-worker', (string)$e);
            }
        }

        $properties = [];

        $systemColumns = $this->getSystemAttributes();
        $columnConfig = $index->getColumns();

        foreach ($systemColumns as $column => $type) {
            $properties[$column] = [
                'type' => $this->renderFieldType($type)
            ];
        }

        foreach ($columnConfig as $column) {
            if ($column instanceof IndexColumnInterface) {
                $properties[$column->getName()] = [
                    'type' => $this->renderFieldType($column->getColumnType())
                ];
            }
        }
        Simple::log('elastic-worker', serialize($properties));
        $params = [
            'index' => $tableName,
            'type' => "coreshop",
            'include_type_name' => true,
            'body'  => [
                'coreshop' => [
                    'properties' => $properties
                ]
            ]
        ];

        try {
            $this->client->indices()->putMapping($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
/*
        $tableName = $this->getTablename($index->getName());
        $localizedTableName = $this->getLocalizedTablename($index->getName());
        $relationalTableName = $this->getRelationTablename($index->getName());

        $this->createTableSchema($index, $tableName);*/
    }

    protected function createTableSchema(IndexInterface $index, string $tableName)
    {
        $params = ['index' => $tableName];

        try {
            $this->client->indices()->delete($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }

        try {
            $result = $this->client->indices()->exists(['index' => $tableName]);
        } catch (\Exception $e) {
            $result = null;
            Simple::log('elastic-worker', (string)$e);
        }

        if (!$result) {
            $result = $this->client->indices()->create(
                [
                    'index' => $tableName,
                    'body' => [
                        'settings' => [
                            "number_of_shards" => 5,
                            "number_of_replicas" => 0
                        ]
                    ]
                ]
            );

            Simple::log('elastic-worker','Creating new Index. Name: ' . $tableName);

            if (!$result['acknowledged']) {
                throw new \Exception("Index creation failed. IndexName: " . $tableName);
            }
        } else {
            try {
                $this->client->indices()->delete(['index' => $tableName]);
            } catch (\Exception $e) {
                Simple::log('elastic-worker', (string)$e);
            }
        }

        $properties = [];

        $systemColumns = $this->getSystemAttributes();
        $columnConfig = $index->getColumns();

        foreach ($systemColumns as $column => $type) {
            $properties[$column] = [
                'type' => $this->renderFieldType($type)
            ];
        }

        foreach ($columnConfig as $column) {
            if ($column instanceof IndexColumnInterface) {
                $properties[$column->getName()] = [
                    'type' => $this->renderFieldType($column->getColumnType())
                ];
            }
        }
        Simple::log('elastic-worker', serialize($properties));
        $params = [
            'index' => $tableName,
            'type' => "coreshop",
            'include_type_name' => true,
            'body'  => [
                'coreshop' => [
                    'properties' => $properties
                ]
            ]
        ];

        try {
            $this->client->indices()->putMapping($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }


        $table = $tableSchema->createTable($this->getTablename($index->getName()));
        $table->addOption('row_format', 'DYNAMIC');

        $table->addColumn('o_id', 'integer');
        $table->addColumn('o_key', 'string');
        $table->addColumn('o_virtualObjectId', 'integer');
        $table->addColumn('o_virtualObjectActive', 'boolean');
        $table->addColumn('o_classId', 'integer');
        $table->addColumn('o_className', 'string');
        $table->addColumn('o_type', 'string');
        $table->addColumn('active', 'boolean');
        $table->setPrimaryKey(['o_id']);

        foreach ($index->getColumns() as $column) {
            if ($column instanceof IndexColumnInterface) {
                $type = $column->getObjectType();
                $interpreterClass = $column->hasInterpreter() ? $this->getInterpreterObject($column) : null;
                if ($type !== 'localizedfields' && !$interpreterClass instanceof LocalizedInterpreterInterface) {
                    $table->addColumn($column->getName(), $this->renderFieldType($column->getColumnType()), $this->getFieldTypeConfig($column));
                }
            }
        }

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getSystemColumns() as $name => $type) {
                    $table->addColumn($name, $this->renderFieldType($type), $this->getSystemFieldTypeConfig($index, $name, $type));
                }
            }
        }

        if (array_key_exists('indexes', $index->getConfiguration())) {
            /**
             * @var TableIndex $tableIndex
             */
            foreach ($index->getConfiguration()['indexes'] as $tableIndex) {
                if ($tableIndex->getType() === TableIndex::TABLE_INDEX_TYPE_UNIQUE) {
                    $table->addUniqueIndex($tableIndex->getColumns());
                } else {
                    $table->addIndex($tableIndex->getColumns());
                }
            }
        }

        return $tableSchema;
    }

    protected function createLocalizedTableSchema(IndexInterface $index, Schema $tableSchema)
    {
        $table = $tableSchema->createTable($this->getLocalizedTablename($index->getName()));
        $table->addOption('row_format', 'DYNAMIC');

        $table->addColumn('oo_id', 'integer');
        $table->addColumn('language', 'string');
        $table->addColumn('name', 'string', ['notnull' => false]);
        $table->setPrimaryKey(['oo_id', 'language']);
        $table->addIndex(['oo_id']);
        $table->addIndex(['language']);

        foreach ($index->getColumns() as $column) {
            $type = $column->getObjectType();
            $interpreterClass = $column->hasInterpreter() ? $this->getInterpreterObject($column) : null;
            if ($type === 'localizedfields' || $interpreterClass instanceof LocalizedInterpreterInterface) {
                $table->addColumn($column->getName(), $this->renderFieldType($column->getColumnType()), $this->getFieldTypeConfig($column));
            }
        }

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getLocalizedSystemColumns() as $name => $type) {
                    $config = ['notnull' => false];

                    if ($extension instanceof IndexSystemColumnTypeConfigExtension) {
                        $config = array_merge($config, $extension->getSystemColumnConfig($name, $type));
                    }

                    $table->addColumn($name, $this->renderFieldType($type), $config);
                }
            }
        }

        if (array_key_exists('localizedIndexes', $index->getConfiguration())) {
            /**
             * @var TableIndex $tableIndex
             */
            foreach ($index->getConfiguration()['localizedIndexes'] as $tableIndex) {
                if ($tableIndex->getType() === TableIndex::TABLE_INDEX_TYPE_UNIQUE) {
                    $table->addUniqueIndex($tableIndex->getColumns());
                } else {
                    $table->addIndex($tableIndex->getColumns());
                }
            }
        }

        return $tableSchema;
    }

    protected function createRelationalTableSchema(IndexInterface $index, Schema $tableSchema)
    {
        $table = $tableSchema->createTable($this->getRelationTablename($index->getName()));
        $table->addOption('row_format', 'DYNAMIC');

        $table->addColumn('src', 'integer');
        $table->addColumn('src_virtualObjectId', 'integer');
        $table->addColumn('dest', 'integer');
        $table->addColumn('fieldname', 'string');
        $table->addColumn('type', 'string');
        $table->setPrimaryKey(['src', 'dest', 'fieldname', 'type']);

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexRelationalColumnsExtensionInterface) {
                foreach ($extension->getRelationalColumns() as $name => $type) {
                    $config = ['notnull' => false];

                    if ($extension instanceof IndexSystemColumnTypeConfigExtension) {
                        $config = array_merge($config, $extension->getSystemColumnConfig($name, $type));
                    }

                    $table->addColumn($name, $this->renderFieldType($type), $config);
                }
            }
        }

        return $tableSchema;
    }

    protected function createLocalizedViews(IndexInterface $index)
    {
        $queries = [];
        $languages = Tool::getValidLanguages(); //TODO: Use Locale Service

        foreach ($languages as $language) {
            $localizedTable = $this->getLocalizedTablename($index->getName());
            $localizedViewName = $this->getLocalizedViewName($index->getName(), $language);
            $tableName = $this->getTableName($index->getName());

            // create view
            $viewQuery = <<<QUERY
            CREATE OR REPLACE VIEW `{$localizedViewName}` AS

            SELECT *
            FROM `{$tableName}`
            LEFT JOIN {$localizedTable}
                ON(
                    {$tableName}.o_id = {$localizedTable}.oo_id AND
                    {$localizedTable}.language = '{$language}'
                )
            QUERY;

            $queries[] = $viewQuery;
        }

        return $queries;
    }

    protected function typeCastValues(IndexColumnInterface $column, $value)
    {
        $doctrineType = $this->renderFieldType($column->getColumnType());

        $type = Type::getType($doctrineType);

        return $type->convertToDatabaseValue($value, $this->database->getDatabasePlatform());
    }

    protected function typeCastValueSQLDecleration(IndexColumnInterface $column)
    {
        $doctrineType = $this->renderFieldType($column->getColumnType());

        $type = Type::getType($doctrineType);

        return $type->convertToDatabaseValueSQL('?', $this->database->getDatabasePlatform());
    }

    protected function handleArrayValues(IndexInterface $index, array $value)
    {
        return ',' . implode(',', $value) . ',';
    }

    public function deleteIndexStructures(IndexInterface $index)
    {
        try {
            $this->client->indices()->delete([
                'index' => $this->getTablename($index->getName())
            ]);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
    }

    public function renameIndexStructures(IndexInterface $index, string $oldName, string $newName): void
    {
        try {
            $languages = Tool::getValidLanguages();
            $potentialTables = [
                $this->getTablename($oldName) => $this->getTablename($newName),
                $this->getLocalizedTablename($oldName) => $this->getLocalizedTablename($newName),
                $this->getRelationTablename($oldName) => $this->getRelationTablename($newName),
            ];
            $allViews = $this->database->getSchemaManager()->listViews();

            foreach ($languages as $language) {
                $potentialTables[$this->getLocalizedViewName($oldName, $language)] = $this->getLocalizedViewName($newName, $language);
            }

            foreach ($potentialTables as $oldTable => $newTable) {
                if (array_key_exists($oldTable, $allViews) || $this->database->getSchemaManager()->tablesExist($oldTable)) {
                    $this->database->executeQuery(
                        sprintf(
                            'RENAME TABLE `%s` TO `%s`',
                            $oldTable,
                            $newTable
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
    }

    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $params = [
            'index' => $this->getTablename($index->getName()),
            'type' => 'coreshop',
            'id' => $object->getId()
        ];

        $this->client->delete($params);
    }

    public function updateIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $doIndex = $object->getIndexable($index);

        if ($doIndex) {
            $preparedData = $this->prepareData($index, $object);

            try {
                $params = [
                    'index' => $this->getTablename($index->getName()),
                    'type' => 'coreshop',
                    'id' => $object->getId(),
                    'body' => $preparedData['data']
                ];

                $this->client->index($params);
            } catch (\Exception $e) {
                $this->logger->warning('Error during updating index table: '.$e);
            }

            //TODO add localized

        } else {
            $this->logger->info('Don\'t adding object ' . $object->getId() . ' to index.');

            $this->deleteFromIndex($index, $object);
        }
    }

    protected function doInsertData(IndexInterface $index, array $data): void
    {
        //insert index data
        $dataKeys = [];
        $updateData = [];
        $insertData = [];
        $insertStatement = [];

        $columns = $index->getColumns()->toArray();
        $columnNames = array_map(function (IndexColumnInterface $column) { return $column->getName(); }, $columns);

        foreach ($data as $key => $value) {
            if (in_array($key, $columnNames)) {
                continue;
            }

            $dataKeys[$this->database->quoteIdentifier($key)] = '?';
            $updateData[] = $value;
            $insertStatement[] = $this->database->quoteIdentifier($key) . ' = ?';
            $insertData[] = $value;
        }

        foreach ($columns as $column) {
            if (!array_key_exists($column->getName(), $data)) {
                continue;
            }

            $value = $data[$column->getName()];

            $dataKeys[$this->database->quoteIdentifier($column->getName())] = $this->typeCastValueSQLDecleration($column);
            $updateData[] = $value;
            $insertStatement[] = $this->database->quoteIdentifier($column->getName()) . ' = ' . $this->typeCastValueSQLDecleration($column);
            $insertData[] = $value;
        }

        $insert = 'INSERT INTO ' . $this->getTablename($index->getName()) . ' (' . implode(',', array_keys($dataKeys)) . ') VALUES (' . implode(',', $dataKeys) . ')'
            . ' ON DUPLICATE KEY UPDATE ' . implode(',', $insertStatement);

        $this->database->executeQuery($insert, array_merge($updateData, $insertData));
    }

    protected function doInsertRelationalData(IndexInterface $index, $data): void
    {
        foreach ($data as $rd) {
            $this->database->insert($this->getRelationTablename($index->getName()), $rd);
        }
    }

    protected function doInsertLocalizedData(IndexInterface $index, array $data): void
    {
        $columns = $index->getColumns()->toArray();
        $columnNames = array_map(function (IndexColumnInterface $column) { return $column->getName(); }, $columns);

        foreach ($data['values'] as $language => $values) {
            $dataKeys = [
                'oo_id' => '?',
                'language' => '?',
            ];
            $updateData = [
                $data['oo_id'],
                $language,
            ];
            $insertStatement = [
                'oo_id = ?',
                'language = ?',
            ];
            $insertData = [
                $data['oo_id'],
                $language,
            ];

            foreach ($values as $key => $value) {
                if (in_array($key, $columnNames)) {
                    continue;
                }

                $dataKeys[$this->database->quoteIdentifier($key)] = '?';
                $updateData[] = $value;
                $insertStatement[] = $this->database->quoteIdentifier($key) . ' = ?';
                $insertData[] = $value;
            }

            foreach ($index->getColumns() as $column) {
                if (!array_key_exists($column->getName(), $values)) {
                    continue;
                }

                $value = $values[$column->getName()];

                $dataKeys[$this->database->quoteIdentifier($column->getName())] = $this->typeCastValueSQLDecleration($column);
                $updateData[] = $value;

                $insertStatement[] = $this->database->quoteIdentifier($column->getName()) . ' = ' . $this->typeCastValueSQLDecleration($column);
                $insertData[] = $value;
            }

            $insert = 'INSERT INTO ' . $this->getLocalizedTablename($index->getName()) . ' (' . implode(',', array_keys($dataKeys)) . ') VALUES (' . implode(',', $dataKeys) . ')'
                . ' ON DUPLICATE KEY UPDATE ' . implode(',', $insertStatement);

            $this->database->executeQuery($insert, array_merge($updateData, $insertData));
        }
    }

    public function renderFieldType(string $type)
    {
        switch ($type) {
            case IndexColumnInterface::FIELD_TYPE_INTEGER:
                return "integer";

            case IndexColumnInterface::FIELD_TYPE_BOOLEAN:
                return "boolean";

            case IndexColumnInterface::FIELD_TYPE_DATE:
                return "date";

            case IndexColumnInterface::FIELD_TYPE_DOUBLE:
                return "dizbke";

            case IndexColumnInterface::FIELD_TYPE_STRING:
                return "text";

            case IndexColumnInterface::FIELD_TYPE_TEXT:
                return "text";
        }

        throw new \Exception($type . " is not supported by Elasticsearch Index");
    }

    public function getFieldTypeConfig(IndexColumnInterface $column)
    {
        $config = ['notnull' => false];

        foreach ($this->getExtensions($column->getIndex()) as $extension) {
            if ($extension instanceof IndexColumnTypeConfigExtension) {
                $config = array_merge($config, $extension->getColumnConfig($column));
            }
        }

        return $config;
    }

    public function getSystemFieldTypeConfig(IndexInterface $index, string $name, string $type)
    {
        $config = ['notnull' => false];

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexSystemColumnTypeConfigExtension) {
                $config = array_merge($config, $extension->getSystemColumnConfig($name, $type));
            }
        }

        return $config;
    }

    public function getList(IndexInterface $index): ListingInterface
    {
        return new ElasticsearchWorker\Listing($index, $this, $this->database);
    }

    public function getTablename(string $name): string
    {
        return 'coreshop_index_elasticsearch_' . strtolower($name);
    }

    public function getLocalizedTablename(string $name): string
    {
        return 'coreshop_index_elasticsearch_localized_' . strtolower($name);
    }

    public function getLocalizedViewName(string $name, string $language): string
    {
        return $this->getLocalizedTablename($name) . '_' . $language;
    }

    public function getRelationTablename(string $name): string
    {
        return 'coreshop_index_elasticsearch_relations_' . strtolower($name);
    }
}
