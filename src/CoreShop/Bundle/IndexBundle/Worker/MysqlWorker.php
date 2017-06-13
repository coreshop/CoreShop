<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Bundle\IndexBundle\Condition\MysqlRenderer;
use CoreShop\Component\Index\ClassHelper\ClassHelperInterface;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Logger;
use Pimcore\Tool;

class MysqlWorker extends AbstractWorker
{
    /**
     * Database.
     *
     * @var \Pimcore\Db\Connection
     */
    protected $database;

    public function __construct(
        ServiceRegistryInterface $classHelperRegistry,
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry
    )
    {
        parent::__construct($classHelperRegistry, $getterServiceRegistry, $interpreterServiceRegistry);

        $this->database = \Pimcore\Db::get();
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdateIndexStructures(IndexInterface $index)
    {
        $this->createTables($index);
        $this->processTable($index);
        $this->processLocalizedTable($index);
        $this->createLocalizedViews($index);
    }

    /**
     * Process Table - delete/add missing/new columns.
     *
     * @param IndexInterface $index
     */
    protected function processTable(IndexInterface $index)
    {
        $classHelper = $this->classHelperRegistry->has($index->getClass()) ? $this->classHelperRegistry->get($index->getClass()) : null;

        $columns = $this->getTableColumns($this->getTablename($index));
        $columnsToDelete = $columns;
        $columnsToAdd = [];

        $columnConfig = $index->getColumns();

        foreach ($columnConfig as $column) {
            if ($column instanceof IndexColumnInterface) {
                $type = $column->getType();
                $columnTypeForIndex = $this->renderFieldType($column->getColumnType());

                if ($type !== 'localizedfields') {
                    if (!array_key_exists($column->getName(), $columns)) {
                        $columnsToAdd[$column->getName()] = $columnTypeForIndex;
                    }
                }

                unset($columnsToDelete[$column->getName()]);
            }
        }

        if ($classHelper instanceof ClassHelperInterface) {
            foreach ($classHelper->getSystemColumns() as $name => $type) {
                if (!array_key_exists($name, $columns)) {
                    $columnTypeForIndex = $this->renderFieldType($type);
                    $columnsToAdd[$name] = $columnTypeForIndex;
                }

                unset($columnsToDelete[$name]);
            }
        }

        $this->dropColumns($this->getTablename($index), $columnsToDelete);
        $this->addColumns($this->getTablename($index), $columnsToAdd);
    }

    /**
     * Process Localized Table - delete/add missing/new columns.
     *
     * @param IndexInterface $index
     */
    protected function processLocalizedTable(IndexInterface $index)
    {
        $classHelper = $this->classHelperRegistry->has($index->getClass()) ? $this->classHelperRegistry->get($index->getClass()) : null;

        $localizedColumns = $this->getTableColumns($this->getLocalizedTablename($index));
        $localizedColumnsToAdd = [];
        $localizedColumnsToDelete = $localizedColumns;

        $columnConfig = $index->getColumns();

        foreach ($columnConfig as $column) {
            $type = $column->getType();

            if ($type === 'localizedfield') {
                $columnTypeForIndex = $this->renderFieldType($column->getColumnType());

                if (!array_key_exists($column->getName(), $localizedColumns)) {
                    $localizedColumnsToAdd[$column->getName()] = $columnTypeForIndex;
                }

                unset($localizedColumnsToDelete[$column->getName()]);
            }
        }

        if ($classHelper instanceof ClassHelperInterface) {
            foreach ($classHelper->getLocalizedSystemColumns() as $name => $type) {
                if (!array_key_exists($name, $localizedColumns)) {
                    $columnTypeForIndex = $this->renderFieldType($type);
                    $localizedColumnsToAdd[$name] = $columnTypeForIndex;
                }

                unset($localizedColumnsToDelete[$name]);
            }
        }

        $this->dropColumns($this->getLocalizedTablename($index), $localizedColumnsToDelete);
        $this->addColumns($this->getLocalizedTablename($index), $localizedColumnsToAdd);
    }

    /**
     * get all columns from table.
     *
     * @param $table
     *
     * @return array
     */
    protected function getTableColumns($table)
    {
        $data = $this->database->fetchAll('SHOW COLUMNS FROM ' . $table);

        $columns = [];

        foreach ($data as $d) {
            $columns[$d['Field']] = $d['Field'];
        }

        return $columns;
    }

    /**
     * @param $table
     * @param $columns
     */
    protected function addColumns($table, $columns)
    {
        foreach ($columns as $c => $type) {
            $this->addColumn($table, $c, $type);
        }
    }

    /**
     * @param $table
     * @param $columns
     */
    protected function dropColumns($table, $columns)
    {
        $systemColumns = $this->getSystemAttributes();
        $systemLocalizedColumns = $this->getLocalizedSystemAttributes();

        foreach ($columns as $c) {
            if (!array_key_exists($c, $systemColumns) && !array_key_exists($c, $systemLocalizedColumns)) {
                $this->dropColumn($table, $c);
            }
        }
    }

    /**
     * @param $table
     * @param $column
     */
    protected function dropColumn($table, $column)
    {
        $this->database->query('ALTER TABLE `' . $table . '` DROP COLUMN `' . $column . '`;');
    }

    /**
     * @param $table
     * @param $column
     * @param $type
     */
    protected function addColumn($table, $column, $type)
    {
        $this->database->query('ALTER TABLE `' . $table . '` ADD `' . $column . '` ' . $type . ';');
    }

    /**
     * Create Tables of not exists.
     *
     * @param IndexInterface $index
     */
    protected function createTables(IndexInterface $index)
    {
        $this->database->query('CREATE TABLE IF NOT EXISTS `' . $this->getTablename($index) . "` (
          `o_id` INT(11) NOT NULL DEFAULT '0',
          `o_key` VARCHAR(255) NOT NULL,
          `o_virtualProductId` INT(11) NOT NULL,
          `o_virtualProductActive` TINYINT(1) NOT NULL,
          `o_classId` INT(11) NOT NULL,
          `o_className` VARCHAR(255) NOT NULL,
          `o_type` VARCHAR(20) NOT NULL,
          `active` TINYINT(1) NOT NULL,
          PRIMARY KEY  (`o_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->database->query('CREATE TABLE IF NOT EXISTS `' . $this->getLocalizedTablename($index) . "` (
		  `oo_id` INT(11) NOT NULL DEFAULT '0',
		  `language` VARCHAR(10) NOT NULL DEFAULT '',
		  `name` VARCHAR(255) NULL,
		  PRIMARY KEY (`oo_id`,`language`),
          INDEX `ooo_id` (`oo_id`),
          INDEX `language` (`language`)
		) DEFAULT CHARSET=utf8;");

        $this->database->query('CREATE TABLE IF NOT EXISTS `' . $this->getRelationTablename($index) . "` (
          `src` INT(11) NOT NULL DEFAULT '0',
          `src_virtualProductId` INT(11) NOT NULL,
          `dest` INT(11) NOT NULL,
          `fieldname` VARCHAR(255) COLLATE utf8_bin NOT NULL,
          `type` VARCHAR(20) COLLATE utf8_bin NOT NULL,
          PRIMARY KEY (`src`,`dest`,`fieldname`,`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
    }

    /**
     * Create Localized Views.
     *
     * @param IndexInterface $index
     */
    protected function createLocalizedViews(IndexInterface $index)
    {
        // init
        $languages = Tool::getValidLanguages(); //TODO: Use Locale Service

        foreach ($languages as $language) {
            try {
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

                $this->database->query($viewQuery);
            } catch (\Exception $e) {
                Logger::error($e);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndexStructures(IndexInterface $index)
    {
        try {
            $languages = Tool::getValidLanguages();

            foreach ($languages as $language) {
                $this->database->query('DROP VIEW IF EXISTS `' . $this->getLocalizedViewName($index, $language) . '`');
            }

            $this->database->query('DROP TABLE IF EXISTS `' . $this->getTablename($index) . '`');
            $this->database->query('DROP TABLE IF EXISTS `' . $this->getLocalizedTablename($index) . '`');
            $this->database->query('DROP TABLE IF EXISTS `' . $this->getRelationTablename($index) . '`');
        } catch (\Exception $e) {
            Logger::error($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object)
    {
        $this->database->delete($this->getTablename($index), 'o_id = ' . $this->database->quote($object->getId()));
        $this->database->delete($this->getLocalizedTablename($index), 'o_id = ' . $this->database->quote($object->getId()));
        $this->database->delete($this->getRelationTablename($index), 'src = ' . $this->database->quote($object->getId()));
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
                Logger::warn('Error during updating index table: ' . $e);
            }

            try {
                $this->doInsertLocalizedData($index, $preparedData['localizedData']);
            } catch (\Exception $e) {
                Logger::warn('Error during updating index table: ' . $e);
            }

            try {
                $this->database->delete($this->getRelationTablename($index), ['src' => $this->database->quote($object->getId())]);
                foreach ($preparedData['relation'] as $rd) {
                    $this->database->insert($this->getRelationTablename($index), $rd);
                }
            } catch (\Exception $e) {
                Logger::warn('Error during updating index relation table: ' . $e->getMessage(), $e);
            }
        } else {
            Logger::info("Don't adding product " . $object->getId() . ' to index.');

            $this->deleteFromIndex($index, $object);
        }
    }

    /**
     * Insert data into mysql-table.
     *
     * @param IndexInterface $index
     * @param $data
     */
    protected function doInsertData(IndexInterface $index, $data)
    {
        //insert index data
        $dataKeys = [];
        $updateData = [];
        $insertData = [];
        $insertStatement = [];

        foreach ($data as $key => $d) {
            $dataKeys[$this->database->quoteIdentifier($key)] = '?';
            $updateData[] = $d;
            $insertStatement[] = $this->database->quoteIdentifier($key) . ' = ?';
            $insertData[] = $d;
        }

        $insert = 'INSERT INTO ' . $this->getTablename($index) . ' (' . implode(',', array_keys($dataKeys)) . ') VALUES (' . implode(',', $dataKeys) . ')'
            . ' ON DUPLICATE KEY UPDATE ' . implode(',', $insertStatement);

        $this->database->query($insert, array_merge($updateData, $insertData));
    }

    /**
     * Insert data into mysql-table.
     *
     * @param IndexInterface $index
     * @param $data
     */
    protected function doInsertLocalizedData(IndexInterface $index, $data)
    {
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
                'oo_id=?',
                'language=?',
            ];
            $insertData = [
                $data['oo_id'],
                $language,
            ];

            foreach ($values as $key => $d) {
                $dataKeys[$this->database->quoteIdentifier($key)] = '?';
                $updateData[] = $d;

                $insertStatement[] = $this->database->quoteIdentifier($key) . ' = ?';
                $insertData[] = $d;
            }

            $insert = 'INSERT INTO ' . $this->getLocalizedTablename($index) . ' (' . implode(',', array_keys($dataKeys)) . ') VALUES (' . implode(',', $dataKeys) . ')'
                . ' ON DUPLICATE KEY UPDATE ' . implode(',', $insertStatement);

            $this->database->query($insert, array_merge($updateData, $insertData));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function renderCondition(ConditionInterface $condition)
    {
        $renderer = new MysqlRenderer();

        return $renderer->render($condition);
    }

    /**
     * {@inheritdoc}
     */
    public function renderFieldType($type)
    {
        switch ($type) {
            case IndexColumnInterface::FIELD_TYPE_INTEGER:
                return 'INT(11)';

            case IndexColumnInterface::FIELD_TYPE_BOOLEAN:
                return 'INT(1)';

            case IndexColumnInterface::FIELD_TYPE_DATE:
                return 'DATETIME';

            case IndexColumnInterface::FIELD_TYPE_DOUBLE:
                return 'DOUBLE';

            case IndexColumnInterface::FIELD_TYPE_STRING:
                return 'VARCHAR(255)';

            case IndexColumnInterface::FIELD_TYPE_TEXT:
                return 'TEXT';
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
     * @param $language
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
