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

namespace CoreShop\IndexService;

use CoreShop\Exception;
use CoreShop\IndexService\Condition\Mysql as ConditionRenderer;
use CoreShop\Model\Index;
use CoreShop\Model\Product;
use CoreShop\Model\Index\Config\Column;
use Pimcore\Db;
use Pimcore\Logger;
use Pimcore\Tool;

/**
 * Class Mysql
 * @package CoreShop\IndexService
 */
class Mysql extends AbstractWorker
{
    /**
     * @var string
     */
    public static $type = 'mysql';

    /**
     * Database.
     *
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $db;

    /**
     * Mysql constructor.
     *
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        parent::__construct($index);

        $this->db = Db::get();
    }

    /**
     * Create Database index table.
     */
    public function createOrUpdateIndexStructures()
    {
        $this->createTables();
        $this->processTable();
        $this->processLocalizedTable();
        $this->createLocalizedViews();
    }

    /**
     * Process Table - delete/add missing/new columns
     */
    protected function processTable()
    {
        $columns = $this->getTableColumns($this->getTablename());
        $columnsToDelete = $columns;
        $columnsToAdd = [];

        $columnConfig = $this->getColumnsConfiguration();

        foreach ($columnConfig as $column) {
            if ($column instanceof Column) {
                $columnTypeForIndex = $this->renderFieldType($column->getColumnType());

                if (!$column instanceof Column\Localizedfields) {
                    if (!array_key_exists($column->getName(), $columns)) {
                        $columnsToAdd[$column->getName()] = $columnTypeForIndex;
                    }
                }

                unset($columnsToDelete[$column->getName()]);
            }
        }

        $this->dropColumns($this->getTablename(), $columnsToDelete);
        $this->addColumns($this->getTablename(), $columnsToAdd);
    }

    /**
     * Process Localized Table - delete/add missing/new columns
     */
    protected function processLocalizedTable()
    {
        $localizedColumns = $this->getTableColumns($this->getLocalizedTablename());
        $localizedColumnsToAdd = [];
        $localizedColumnsToDelete = $localizedColumns;

        $columnConfig = $this->getColumnsConfiguration();

        foreach ($columnConfig as $column) {
            if ($column instanceof Index\Config\Column\Localizedfields) {
                $columnTypeForIndex = $this->renderFieldType($column->getColumnType());

                if (!array_key_exists($column->getName(), $localizedColumns)) {
                    $localizedColumnsToAdd[$column->getName()] = $columnTypeForIndex;
                }

                unset($localizedColumnsToDelete[$column->getName()]);
            }
        }

        $this->dropColumns($this->getLocalizedTablename(), $localizedColumnsToDelete);
        $this->addColumns($this->getLocalizedTablename(), $localizedColumnsToAdd);
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
     */
    protected function createTables()
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getTablename()."` (
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

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getLocalizedTablename()."` (
		  `oo_id` int(11) NOT NULL default '0',
		  `language` varchar(10) NOT NULL DEFAULT '',
		  `name` varchar(255) NOT NULL,
		  PRIMARY KEY (`oo_id`,`language`),
          INDEX `ooo_id` (`oo_id`),
          INDEX `language` (`language`)
		) DEFAULT CHARSET=utf8;");

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getRelationTablename()."` (
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
     */
    protected function createLocalizedViews()
    {
        // init
        $languages = Tool::getValidLanguages();

        foreach ($languages as $language) {
            try {
                $localizedTable = $this->getLocalizedTablename();
                $localizedViewName = $this->getLocalizedViewName($language);
                $tableName = $this->getTableName();

                // create view
                $viewQuery = <<<QUERY
CREATE OR REPLACE SQL SECURITY INVOKER VIEW `{$localizedViewName}` AS

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
     * deletes necessary index structuers (like database tables).
     *
     * @return mixed
     */
    public function deleteIndexStructures()
    {
        try {
            $languages = Tool::getValidLanguages();

            foreach ($languages as $language) {
                $this->db->query('DROP VIEW IF EXISTS `'.$this->getLocalizedViewName($language).'`');
            }

            $this->db->query('DROP TABLE IF EXISTS `'.$this->getTablename().'`');
            $this->db->query('DROP TABLE IF EXISTS `'.$this->getLocalizedTablename().'`');
            $this->db->query('DROP TABLE IF EXISTS `'.$this->getRelationTablename().'`');
        } catch (\Exception $e) {
            Logger::error($e);
        }
    }

    /**
     * Delete Product from index.
     *
     * @param Product $object
     */
    public function deleteFromIndex(Product $object)
    {
        $this->db->delete($this->getTablename(), 'o_id = '.$this->db->quote($object->getId()));
        $this->db->delete($this->getLocalizedTablename(), 'o_id = '.$this->db->quote($object->getId()));
        $this->db->delete($this->getRelationTablename(), 'src = '.$this->db->quote($object->getId()));
    }

    /**
     * Update or create product in index.
     *
     * @param Product $object
     */
    public function updateIndex(Product $object)
    {
        if ($object->getDoIndex()) {
            $preparedData = $this->prepareData($object);

            try {
                $this->doInsertData($preparedData['data']);
            } catch (\Exception $e) {
                Logger::warn('Error during updating index table: '.$e);
            }

            try {
                $this->doInsertLocalizedData($preparedData['localizedData']);
            } catch (\Exception $e) {
                Logger::warn('Error during updating index table: '.$e);
            }

            try {
                $this->db->delete($this->getRelationTablename(), 'src = '.$this->db->quote($object->getId()));
                foreach ($preparedData['relation'] as $rd) {
                    $this->db->insert($this->getRelationTablename(), $rd);
                }
            } catch (\Exception $e) {
                Logger::warn('Error during updating index relation table: '.$e->getMessage(), $e);
            }
        } else {
            Logger::info("Don't adding product ".$object->getId().' to index.');

            $this->deleteFromIndex($object);
        }
    }

    /**
     * Insert data into mysql-table.
     *
     * @param $data
     */
    protected function doInsertData($data)
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

        $insert = 'INSERT INTO '.$this->getTablename().' ('.implode(',', array_keys($dataKeys)).') VALUES ('.implode(',', $dataKeys).')'
            .' ON DUPLICATE KEY UPDATE '.implode(',', $insertStatement);

        $this->db->query($insert, array_merge($updateData, $insertData));
    }


    /**
     * Insert data into mysql-table.
     *
     * @param $data
     */
    protected function doInsertLocalizedData($data)
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

            $insert = 'INSERT INTO '.$this->getLocalizedTablename().' ('.implode(',', array_keys($dataKeys)).') VALUES ('.implode(',', $dataKeys).')'
                .' ON DUPLICATE KEY UPDATE '.implode(',', $insertStatement);

            $this->db->query($insert, array_merge($updateData, $insertData));
        }
    }

    /**
     * Renders a condition to MySql
     *
     * @param Condition $condition
     * @return string
     * @throws Exception
     */
    public function renderCondition(Condition $condition)
    {
        $renderer = new ConditionRenderer();

        return $renderer->render($condition);
    }

    /**
     * get type for index
     *
     * @param $type
     * @return string
     * @throws \Exception
     */
    public function renderFieldType($type)
    {
        switch ($type) {
            case Column::FIELD_TYPE_INTEGER:
                return "INT(11)";

            case Column::FIELD_TYPE_BOOLEAN:
                return "INT(1)";

            case Column::FIELD_TYPE_DATE:
                return "DATETIME";

            case Column::FIELD_TYPE_DOUBLE:
                return "DOUBLE";

            case Column::FIELD_TYPE_STRING:
                return "VARCHAR(255)";

            case Column::FIELD_TYPE_TEXT:
                return "TEXT";
        }

        throw new \Exception($type . " is not supported by MySQL Index");
    }

    /**
     * Return Productlist.
     *
     * @return Product\Listing\Mysql
     */
    public function getProductList()
    {
        return new Product\Listing\Mysql($this->getIndex());
    }

    /**
     * get table name.
     *
     * @return string
     */
    public function getTablename()
    {
        return 'coreshop_index_mysql_'.$this->getIndex()->getName();
    }

    /**
     * get table name.
     *
     * @return string
     */
    public function getLocalizedTablename()
    {
        return 'coreshop_index_mysql_localized_'.$this->getIndex()->getName();
    }

    /**
     * get localized view name
     *
     * @param $language
     * @return string
     */
    public function getLocalizedViewName($language)
    {
        return $this->getLocalizedTablename() . "_" . $language;
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
}
