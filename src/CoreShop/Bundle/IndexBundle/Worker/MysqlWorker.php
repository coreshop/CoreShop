<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker\TableIndex;
use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderRendererInterface;
use CoreShop\Component\Index\Worker\FilterGroupHelperInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Pimcore\Tool;

class MysqlWorker extends AbstractWorker
{
    /**
     * Database.
     *
     * @var Connection
     */
    protected $database;

    /**
     * @param ServiceRegistryInterface   $extensionsRegistry
     * @param ServiceRegistryInterface   $getterServiceRegistry
     * @param ServiceRegistryInterface   $interpreterServiceRegistry
     * @param FilterGroupHelperInterface $filterGroupHelper
     * @param ConditionRendererInterface $conditionRenderer
     * @param OrderRendererInterface     $orderRenderer
     * @param Connection                 $connection
     */
    public function __construct(
        ServiceRegistryInterface $extensionsRegistry,
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry,
        FilterGroupHelperInterface $filterGroupHelper,
        ConditionRendererInterface $conditionRenderer,
        OrderRendererInterface $orderRenderer,
        Connection $connection
    ) {
        parent::__construct(
            $extensionsRegistry,
            $getterServiceRegistry,
            $interpreterServiceRegistry,
            $filterGroupHelper,
            $conditionRenderer,
            $orderRenderer
        );

        $this->database = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdateIndexStructures(IndexInterface $index)
    {
        $schemaManager = $this->database->getSchemaManager();

        $tableName = $this->getTablename($index);
        $localizedTableName = $this->getLocalizedTablename($index);
        $relationalTableName = $this->getRelationTablename($index);

        $oldTables = [];

        foreach ([$tableName, $localizedTableName, $relationalTableName] as $searchTableName) {
            if ($schemaManager->tablesExist([$searchTableName])) {
                $oldTables[] = $schemaManager->listTableDetails($searchTableName);
            }
        }

        $newSchema = new Schema();
        $oldSchema = new Schema($oldTables);

        $this->createTableSchema($index, $newSchema);
        $this->createLocalizedTableSchema($index, $newSchema);
        $this->createRelationalTableSchema($index, $newSchema);

        $queries = $newSchema->getMigrateFromSql($oldSchema, $this->database->getDatabasePlatform());

        $this->database->transactional(function () use ($queries, $index) {
            foreach ($queries as $qry) {
                $this->database->executeQuery($qry);
            }

            foreach ($this->createLocalizedViews($index) as $qry) {
                $this->database->executeQuery($qry);
            }
        });
    }

    /**
     * @param IndexInterface $index
     * @param Schema         $tableSchema
     *
     * @return Schema
     *
     * @throws \Exception
     */
    protected function createTableSchema(IndexInterface $index, Schema $tableSchema)
    {
        $table = $tableSchema->createTable($this->getTablename($index));

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
                    $table->addColumn($column->getName(), $this->renderFieldType($column->getColumnType()), ['notnull' => false]);
                }
            }
        }

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getSystemColumns() as $name => $type) {
                    $table->addColumn($name, $this->renderFieldType($type), ['notnull' => false]);
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

    /**
     * @param IndexInterface $index
     * @param Schema         $tableSchema
     *
     * @return Schema
     *
     * @throws \Exception
     */
    protected function createLocalizedTableSchema(IndexInterface $index, Schema $tableSchema)
    {
        $table = $tableSchema->createTable($this->getLocalizedTablename($index));
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
                $table->addColumn($column->getName(), $this->renderFieldType($column->getColumnType()), ['notnull' => false]);
            }
        }

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getLocalizedSystemColumns() as $name => $type) {
                    $table->addColumn($name, $this->renderFieldType($type), ['notnull' => false]);
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

    /**
     * @param IndexInterface $index
     * @param Schema         $tableSchema
     *
     * @return Schema
     */
    protected function createRelationalTableSchema(IndexInterface $index, Schema $tableSchema)
    {
        $table = $tableSchema->createTable($this->getRelationTablename($index));
        $table->addColumn('src', 'integer');
        $table->addColumn('src_virtualObjectId', 'integer');
        $table->addColumn('dest', 'integer');
        $table->addColumn('fieldname', 'string');
        $table->addColumn('type', 'string');
        $table->setPrimaryKey(['src', 'dest', 'fieldname', 'type']);

        return $tableSchema;
    }

    /**
     * Create Localized Views.
     *
     * @param IndexInterface $index
     *
     * @return array
     */
    protected function createLocalizedViews(IndexInterface $index)
    {
        $queries = [];
        $languages = Tool::getValidLanguages(); //TODO: Use Locale Service

        foreach ($languages as $language) {
            $localizedTable = $this->getLocalizedTablename($index);
            $localizedViewName = $this->getLocalizedViewName($index, $language);
            $tableName = $this->getTableName($index);

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

    /**
     * {@inheritdoc}
     */
    protected function typeCastValues(IndexColumnInterface $column, $value)
    {
        $doctrineType = $this->renderFieldType($column->getColumnType());

        $type = Type::getType($doctrineType);

        return $type->convertToDatabaseValue($value, $this->database->getDatabasePlatform());
    }

    /**
     * {@inheritdoc}
     */
    protected function typeCastValueSQLDecleration(IndexColumnInterface $column)
    {
        $doctrineType = $this->renderFieldType($column->getColumnType());

        $type = Type::getType($doctrineType);

        return $type->convertToDatabaseValueSQL('?', $this->database->getDatabasePlatform());
    }

    /**
     * {@inheritdoc}
     */
    protected function handleArrayValues(IndexInterface $index, array $value)
    {
        return ',' . implode($value, ',') . ',';
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndexStructures(IndexInterface $index)
    {
        try {
            $languages = Tool::getValidLanguages();

            foreach ($languages as $language) {
                $this->database->executeQuery('DROP VIEW IF EXISTS `' . $this->getLocalizedViewName($index, $language) . '`');
            }

            $this->database->executeQuery('DROP TABLE IF EXISTS `' . $this->getTablename($index) . '`');
            $this->database->executeQuery('DROP TABLE IF EXISTS `' . $this->getLocalizedTablename($index) . '`');
            $this->database->executeQuery('DROP TABLE IF EXISTS `' . $this->getRelationTablename($index) . '`');
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object)
    {
        $this->database->delete($this->getTablename($index), ['o_id' => $object->getId()]);
        $this->database->delete($this->getLocalizedTablename($index), ['oo_id' => $object->getId()]);
        $this->database->delete($this->getRelationTablename($index), ['src' => $object->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateIndex(IndexInterface $index, IndexableInterface $object)
    {
        $doIndex = $object->getIndexable();

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
                $this->database->delete($this->getRelationTablename($index), ['src' => $object->getId()]);
                foreach ($preparedData['relation'] as $rd) {
                    $this->database->insert($this->getRelationTablename($index), $rd);
                }
            } catch (\Exception $e) {
                $this->logger->warning('Error during updating index relation table: ' . $e->getMessage(), [$e]);
            }
        } else {
            $this->logger->info('Don\'t adding object ' . $object->getId() . ' to index.');

            $this->deleteFromIndex($index, $object);
        }
    }

    /**
     * Insert data into mysql-table.
     *
     * @param IndexInterface $index
     * @param array          $data
     */
    protected function doInsertData(IndexInterface $index, $data)
    {
        //insert index data
        $dataKeys = [];
        $updateData = [];
        $insertData = [];
        $insertStatement = [];

        $columns = $index->getColumns()->toArray();
        $columnNames = array_map(function(IndexColumnInterface $column) { return $column->getName(); }, $columns);

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

        $insert = 'INSERT INTO ' . $this->getTablename($index) . ' (' . implode(',', array_keys($dataKeys)) . ') VALUES (' . implode(',', $dataKeys) . ')'
            . ' ON DUPLICATE KEY UPDATE ' . implode(',', $insertStatement);

        $this->database->executeQuery($insert, array_merge($updateData, $insertData));
    }

    /**
     * Insert data into mysql-table.
     *
     * @param IndexInterface $index
     * @param array          $data
     */
    protected function doInsertLocalizedData(IndexInterface $index, $data)
    {
        $columnNames = array_map(function(IndexColumnInterface $column) { return $column->getName(); }, $columns);

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

            foreach ($columns as $column) {
                if (!array_key_exists($column->getName(), $values)) {
                    continue;
                }

                $value = $values[$column->getName()];

                $dataKeys[$this->database->quoteIdentifier($column->getName())] = $this->typeCastValueSQLDecleration($column);;
                $updateData[] = $value;

                $insertStatement[] = $this->database->quoteIdentifier($column->getName()) . ' = ' . $this->typeCastValueSQLDecleration($column);;
                $insertData[] = $value;
            }

            $insert = 'INSERT INTO ' . $this->getLocalizedTablename($index) . ' (' . implode(',', array_keys($dataKeys)) . ') VALUES (' . implode(',', $dataKeys) . ')'
                . ' ON DUPLICATE KEY UPDATE ' . implode(',', $insertStatement);

            $this->database->executeQuery($insert, array_merge($updateData, $insertData));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function renderFieldType($type)
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

    /**
     * {@inheritdoc}
     */
    public function getList(IndexInterface $index)
    {
        return new MysqlWorker\Listing($index, $this);
    }

    /**
     * get table name.
     *
     * @param IndexInterface $index
     *
     * @return string
     */
    public function getTablename(IndexInterface $index)
    {
        return 'coreshop_index_mysql_' . $index->getName();
    }

    /**
     * get table name.
     *
     * @param IndexInterface $index
     *
     * @return string
     */
    public function getLocalizedTablename(IndexInterface $index)
    {
        return 'coreshop_index_mysql_localized_' . $index->getName();
    }

    /**
     * get localized view name.
     *
     * @param IndexInterface $index
     * @param string         $language
     *
     * @return string
     */
    public function getLocalizedViewName(IndexInterface $index, $language)
    {
        return $this->getLocalizedTablename($index) . '_' . $language;
    }

    /**
     * get tablename for relations.
     *
     * @param IndexInterface $index
     *
     * @return string
     */
    public function getRelationTablename(IndexInterface $index)
    {
        return 'coreshop_index_mysql_relations_' . $index->getName();
    }
}
