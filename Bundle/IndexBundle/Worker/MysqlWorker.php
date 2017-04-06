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

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Bundle\IndexBundle\Condition\MysqlRenderer;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\IndexService\AbstractWorker;
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
    protected $db;

    public function __construct(
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry
    )
    {
        parent::__construct($getterServiceRegistry, $interpreterServiceRegistry);

        $this->db = \Pimcore\Db::get();
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
     * Process Table - delete/add missing/new columns
     *
     * @param IndexInterface $index
     */
    protected function processTable(IndexInterface $index)
    {
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

        $this->dropColumns($this->getTablename($index), $columnsToDelete);
        $this->addColumns($this->getTablename($index), $columnsToAdd);
    }

    /**
     * Process Localized Table - delete/add missing/new columns
     *
     * @param IndexInterface $index
     */
    protected function processLocalizedTable(IndexInterface $index)
    {
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

        $this->dropColumns($this->getLocalizedTablename($index), $localizedColumnsToDelete);
        $this->addColumns($this->getLocalizedTablename($index), $localizedColumnsToAdd);
    }

    /**
     * get all columns from table
     *
     * @param $table
     * @return array
     */
    protected function getTableColumns($table)
    {
        $data = $this->db->fetchAll('SHOW COLUMNS FROM '. $table);

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

        foreach ($columns as $c) {
            if (!array_key_exists($c, $systemColumns)) {
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
        $this->db->query('ALTER TABLE `' . $table . '` DROP COLUMN `' . $column . '`;');
    }

    /**
     * @param $table
     * @param $column
     * @param $type
     */
    protected function addColumn($table, $column, $type)
    {
        $this->db->query('ALTER TABLE `'.$table.'` ADD `'.$column.'` '.$type.';');
    }

    /**
     * Create Tables of not exists
     *
     * @param IndexInterface $index
     */
    protected function createTables(IndexInterface $index)
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getTablename($index)."` (
          `o_id` int(11) NOT NULL default '0',
          `o_key` varchar(255) NOT NULL,
          `o_virtualProductId` int(11) NOT NULL,
          `o_virtualProductActive` TINYINT(1) NOT NULL,
          `o_classId` int(11) NOT NULL,
          `o_type` varchar(20) NOT NULL,
          `categoryIds` varchar(255) NOT NULL,
          `parentCategoryIds` varchar(255) NOT NULL,
          `active` TINYINT(1) NOT NULL,
          `shops` varchar(255) NOT NULL,
          `minPrice` double NOT NULL,
          `maxPrice` double NOT NULL,
          PRIMARY KEY  (`o_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getLocalizedTablename($index)."` (
		  `oo_id` int(11) NOT NULL default '0',
		  `language` varchar(10) NOT NULL DEFAULT '',
		  `name` varchar(255) NOT NULL,
		  PRIMARY KEY (`oo_id`,`language`),
          INDEX `ooo_id` (`oo_id`),
          INDEX `language` (`language`)
		) DEFAULT CHARSET=utf8;");

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getRelationTablename($index)."` (
          `src` int(11) NOT NULL default '0',
          `src_virtualProductId` int(11) NOT NULL,
          `dest` int(11) NOT NULL,
          `fieldname` varchar(255) COLLATE utf8_bin NOT NULL,
          `type` varchar(20) COLLATE utf8_bin NOT NULL,
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

                $this->db->query($viewQuery);
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
                $this->db->query('DROP VIEW IF EXISTS `'.$this->getLocalizedViewName($index, $language).'`');
            }

            $this->db->query('DROP TABLE IF EXISTS `'.$this->getTablename($index).'`');
            $this->db->query('DROP TABLE IF EXISTS `'.$this->getLocalizedTablename($index, $index).'`');
            $this->db->query('DROP TABLE IF EXISTS `'.$this->getRelationTablename($index, $index).'`');
        } catch (\Exception $e) {
            Logger::error($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFromIndex(IndexInterface $index, PimcoreModelInterface $object)
    {
        $this->db->delete($this->getTablename($index), 'o_id = '.$this->db->quote($object->getId()));
        $this->db->delete($this->getLocalizedTablename($index), 'o_id = '.$this->db->quote($object->getId()));
        $this->db->delete($this->getRelationTablename($index), 'src = '.$this->db->quote($object->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function updateIndex(IndexInterface $index, PimcoreModelInterface $object)
    {
        $doIndex = true; //TODO: Refactor, implement IndexableInterface?

        if (method_exists($object, 'getDoIndex')) {
            $doIndex = $object->getDoIndex();
        }

        if ($doIndex) {
            $preparedData = $this->prepareData($index, $object);

            try {
                $this->doInsertData($index, $preparedData['data']);
            } catch (\Exception $e) {
                Logger::warn('Error during updating index table: '.$e);
            }

            try {
                $this->doInsertLocalizedData($index, $preparedData['localizedData']);
            } catch (\Exception $e) {
                Logger::warn('Error during updating index table: '.$e);
            }

            try {
                $this->db->delete($this->getRelationTablename($index), ['src' => $this->db->quote($object->getId())]);
                foreach ($preparedData['relation'] as $rd) {
                    $this->db->insert($this->getRelationTablename($index), $rd);
                }
            } catch (\Exception $e) {
                Logger::warn('Error during updating index relation table: '.$e->getMessage(), $e);
            }
        } else {
            Logger::info("Don't adding product ".$object->getId().' to index.');

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
            $dataKeys[$this->db->quoteIdentifier($key)] = '?';
            $updateData[] = $d;
            $insertStatement[] = $this->db->quoteIdentifier($key).' = ?';
            $insertData[] = $d;
        }

        $insert = 'INSERT INTO '.$this->getTablename($index).' ('.implode(',', array_keys($dataKeys)).') VALUES ('.implode(',', $dataKeys).')'
            .' ON DUPLICATE KEY UPDATE '.implode(',', $insertStatement);

        $this->db->query($insert, array_merge($updateData, $insertData));
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
                'language' => '?'
            ];
            $updateData = [
                $data['oo_id'],
                $language
            ];
            $insertStatement = [
                'oo_id=?',
                'language=?'
            ];
            $insertData = [
                $data['oo_id'],
                $language
            ];

            foreach ($values as $key => $d) {
                $dataKeys[$this->db->quoteIdentifier($key)] = '?';
                $updateData[] = $d;

                $insertStatement[] = $this->db->quoteIdentifier($key).' = ?';
                $insertData[] = $d;
            }

            $insert = 'INSERT INTO '.$this->getLocalizedTablename($index).' ('.implode(',', array_keys($dataKeys)).') VALUES ('.implode(',', $dataKeys).')'
                .' ON DUPLICATE KEY UPDATE '.implode(',', $insertStatement);

            $this->db->query($insert, array_merge($updateData, $insertData));
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
                return "INT(11)";

            case IndexColumnInterface::FIELD_TYPE_BOOLEAN:
                return "INT(1)";

            case IndexColumnInterface::FIELD_TYPE_DATE:
                return "DATETIME";

            case IndexColumnInterface::FIELD_TYPE_DOUBLE:
                return "DOUBLE";

            case IndexColumnInterface::FIELD_TYPE_STRING:
                return "VARCHAR(255)";

            case IndexColumnInterface::FIELD_TYPE_TEXT:
                return "TEXT";
        }

        throw new \Exception($type . " is not supported by MySQL Index");
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
     * @return string
     */
    public function getTablename(IndexInterface $index)
    {
        return 'coreshop_index_mysql_'.$index->getName();
    }

    /**
     * get table name.
     *
     * @param IndexInterface $index
     * @return string
     */
    public function getLocalizedTablename(IndexInterface $index)
    {
        return 'coreshop_index_mysql_localized_'.$index->getName();
    }

    /**
     * get localized view name
     *
     * @param IndexInterface $index
     * @param $language
     * @return string
     */
    public function getLocalizedViewName(IndexInterface $index, $language)
    {
        return $this->getLocalizedTablename($index) . "_" . $language;
    }

    /**
     * get tablename for relations.
     *
     * @param IndexInterface $index
     * @return string
     */
    public function getRelationTablename(IndexInterface $index)
    {
        return 'coreshop_index_mysql_relations_'.$index->getName();
    }
}
