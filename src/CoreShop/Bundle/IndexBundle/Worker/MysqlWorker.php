<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker\TableIndex;
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
use CoreShop\Component\Index\Worker\WorkerDeleteableByIdInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Pimcore\Tool;

class MysqlWorker extends AbstractWorker implements WorkerDeleteableByIdInterface
{
    public function __construct(
        ServiceRegistryInterface $extensionsRegistry,
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry,
        FilterGroupHelperInterface $filterGroupHelper,
        ConditionRendererInterface $conditionRenderer,
        OrderRendererInterface $orderRenderer,
        protected Connection $database,
    ) {
        parent::__construct(
            $extensionsRegistry,
            $getterServiceRegistry,
            $interpreterServiceRegistry,
            $filterGroupHelper,
            $conditionRenderer,
            $orderRenderer,
        );
    }

    public function createOrUpdateIndexStructures(IndexInterface $index): void
    {
        $schemaManager = $this->database->getSchemaManager();

        $tableName = $this->getTablename($index->getName());
        $localizedTableName = $this->getLocalizedTablename($index->getName());
        $relationalTableName = $this->getRelationTablename($index->getName());

        $oldTables = [];

        foreach ([$tableName, $localizedTableName, $relationalTableName] as $searchTableName) {
            if ($schemaManager->tablesExist([$searchTableName])) {
                $oldTables[] = $schemaManager->listTableDetails($searchTableName);
            }
        }

        $newSchema = new Schema([], [], $this->database->getSchemaManager()->createSchemaConfig());
        $oldSchema = new Schema($oldTables, [], $this->database->getSchemaManager()->createSchemaConfig());

        $this->createTableSchema($index, $newSchema);
        $this->createLocalizedTableSchema($index, $newSchema);
        $this->createRelationalTableSchema($index, $newSchema);

        $queries = $newSchema->getMigrateFromSql($oldSchema, $this->database->getDatabasePlatform());

        //Show run in an Transaction, but doctrine transactional does not work with PDO for some odd reason....
        foreach ($queries as $qry) {
            $this->database->executeQuery($qry);
        }

        foreach ($this->createLocalizedViews($index) as $qry) {
            $this->database->executeQuery($qry);
        }
    }

    protected function createTableSchema(IndexInterface $index, Schema $tableSchema)
    {
        $table = $tableSchema->createTable($this->getTablename($index->getName()));
        $table->addOption('row_format', 'DYNAMIC');

        $table->addColumn('o_id', 'integer');
        $table->addColumn('o_key', 'string');
        $table->addColumn('o_virtualObjectId', 'integer');
        $table->addColumn('o_virtualObjectActive', 'boolean');
        $table->addColumn('o_classId', 'string')->setLength(50);
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
            $languages = Tool::getValidLanguages();

            foreach ($languages as $language) {
                $this->database->executeQuery('DROP VIEW IF EXISTS `' . $this->getLocalizedViewName($index->getName(), $language) . '`');
            }

            $this->database->executeQuery('DROP TABLE IF EXISTS `' . $this->getTablename($index->getName()) . '`');
            $this->database->executeQuery('DROP TABLE IF EXISTS `' . $this->getLocalizedTablename($index->getName()) . '`');
            $this->database->executeQuery('DROP TABLE IF EXISTS `' . $this->getRelationTablename($index->getName()) . '`');
        } catch (\Exception $e) {
            $this->logger->error((string) $e);
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
                            $newTable,
                        ),
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->error((string) $e);
        }
    }

    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $this->database->delete($this->getTablename($index->getName()), ['o_id' => $object->getId()]);
        $this->database->delete($this->getLocalizedTablename($index->getName()), ['oo_id' => $object->getId()]);
        $this->database->delete($this->getRelationTablename($index->getName()), ['src' => $object->getId()]);
    }

    public function deleteFromIndexById(IndexInterface $index, int $id): void
    {
        $this->database->delete($this->getTablename($index->getName()), ['o_id' => $id]);
        $this->database->delete($this->getLocalizedTablename($index->getName()), ['oo_id' => $id]);
        $this->database->delete($this->getRelationTablename($index->getName()), ['src' => $id]);
    }

    public function updateIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $doIndex = $object->getIndexable($index);

        if ($doIndex) {
            $preparedData = $this->prepareData($index, $object);

            try {
                $this->doInsertData($index, $preparedData['data']);
            } catch (\Exception $e) {
                $this->logger->warning('Error during updating index table: ' . $e->getMessage(), [$e]);
            }

            try {
                $this->doInsertLocalizedData($index, $preparedData['localizedData']);
            } catch (\Exception $e) {
                $this->logger->warning('Error during updating index table: ' . $e->getMessage(), [$e]);
            }

            try {
                $this->database->delete($this->getRelationTablename($index->getName()), ['src' => $object->getId()]);

                $this->doInsertRelationalData($index, $preparedData['relation']);
            } catch (\Exception $e) {
                $this->logger->warning('Error during updating index relation table: ' . $e->getMessage(), [$e]);
            }
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
        //Check if the Mapping type is available in doctrine
        $doctrineType = strtolower($type);

        switch ($type) {
            case IndexColumnInterface::FIELD_TYPE_DATE:
                $doctrineType = 'date';

                break;
            case IndexColumnInterface::FIELD_TYPE_DOUBLE:
                $doctrineType = 'decimal';

                break;
        }

        if (Type::hasType($doctrineType)) {
            return Type::getType($doctrineType)->getName();
        }

        throw new \Exception($type . ' is not supported by MySQL Index');
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
        return new MysqlWorker\Listing($index, $this, $this->database);
    }

    public function getTablename(string $name): string
    {
        return 'coreshop_index_mysql_' . $name;
    }

    public function getLocalizedTablename(string $name): string
    {
        return 'coreshop_index_mysql_localized_' . $name;
    }

    public function getLocalizedViewName(string $name, string $language): string
    {
        return $this->getLocalizedTablename($name) . '_' . $language;
    }

    public function getRelationTablename(string $name): string
    {
        return 'coreshop_index_mysql_relations_' . $name;
    }
}
