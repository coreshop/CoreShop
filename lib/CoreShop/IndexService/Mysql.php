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

namespace CoreShop\IndexService;

use CoreShop\Model\Index;
use CoreShop\Model\Product;
use CoreShop\Model\Index\Config\Column\Mysql as Column;
use Pimcore\Db;

/**
 * Class Mysql
 * @package CoreShop\IndexService
 */
class Mysql extends AbstractWorker
{
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
        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getTablename()."` (
          `o_id` int(11) NOT NULL default '0',
          `o_classId` int(11) NOT NULL,
          `o_type` varchar(20) NOT NULL,
          `categoryIds` varchar(255) NOT NULL,
          `parentCategoryIds` varchar(255) NOT NULL,
          `active` TINYINT(1) NOT NULL,
          `shops` varchar(255) NOT NULL,
          PRIMARY KEY  (`o_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $data = $this->db->fetchAll('SHOW COLUMNS FROM '.$this->getTablename());
        $columns = array();

        foreach ($data as $d) {
            $columns[$d['Field']] = $d['Field'];
        }

        $systemColumns = $this->getSystemAttributes();
        $columnsToDelete = $columns;
        $columnsToAdd = array();

        $columnConfig = $this->getColumnsConfiguration();

        foreach ($columnConfig as $column) {
            if (!array_key_exists($column->getName(), $columns)) {
                $doAdd = true;

                if ($doAdd) {
                    $columnsToAdd[$column->getName()] = $column->getColumnType();
                }
            }

            unset($columnsToDelete[$column->getName()]);
        }

        foreach ($columnsToDelete as $c) {
            if (!in_array($c, $systemColumns)) {
                $this->db->query('ALTER TABLE `'.$this->getTablename().'` DROP COLUMN `'.$c.'`;');
            }
        }

        foreach ($columnsToAdd as $c => $type) {
            $this->db->query('ALTER TABLE `'.$this->getTablename().'` ADD `'.$c.'` '.$type.';');
        }

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->getRelationTablename()."` (
          `src` int(11) NOT NULL default '0',
          `dest` int(11) NOT NULL,
          `fieldname` varchar(255) COLLATE utf8_bin NOT NULL,
          `type` varchar(20) COLLATE utf8_bin NOT NULL,
          PRIMARY KEY (`src`,`dest`,`fieldname`,`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
    }

    /**
     * deletes necessary index structuers (like database tables).
     *
     * @return mixed
     */
    public function deleteIndexStructures()
    {
        $this->db->query('DROP TABLE IF EXISTS `'.$this->getTablename().'`');
        $this->db->query('DROP TABLE IF EXISTS `'.$this->getRelationTablename().'`');
    }

    /**
     * Delete Product from index.
     *
     * @param Product $object
     */
    public function deleteFromIndex(Product $object)
    {
        $this->db->delete($this->getTablename(), 'o_id = '.$this->db->quote($object->getId()));
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
                \Logger::warn('Error during updating index table: '.$e);
            }

            try {
                $this->db->delete($this->getRelationTablename(), 'src = '.$this->db->quote($object->getId()));
                foreach ($preparedData['relation'] as $rd) {
                    $this->db->insert($this->getRelationTablename(), $rd);
                }
            } catch (\Exception $e) {
                \Logger::warn('Error during updating index relation table: '.$e->getMessage(), $e);
            }
        } else {
            \Logger::info("Don't adding product ".$object->getId().' to index.');

            try {
                $this->db->delete($this->getTablename(), 'o_id = '.$this->db->quote($object->getId()));
            } catch (\Exception $e) {
                \Logger::warn('Error during updating index table: '.$e->getMessage(), $e);
            }

            try {
                $this->db->delete($this->getRelationTablename(), 'src = '.$this->db->quote($object->getId()));
            } catch (\Exception $e) {
                \Logger::warn('Error during updating index relation table: '.$e->getMessage(), $e);
            }
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
     * Renders a condition to MySql
     *
     * @param Condition $condition
     * @return string
     * @throws \Exception
     */
    public function renderCondition(Condition $condition) {
        switch($condition->getType()) {

            case "in":
                $inValues = [];

                foreach ($condition->getValues() as $c => $value) {
                    $inValues[] = Db::get()->quote($value);
                }

                $rendered = 'TRIM(`'.$condition->getFieldName().'`) IN ('.implode(',', $inValues).')';
                break;

            case "match":
                $rendered = 'TRIM(`'.$condition->getFieldName().'`) = '.Db::get()->quote($condition->getValues());
                break;

            case "not-match":
                $rendered = 'TRIM(`'.$condition->getFieldName().'`) != '.Db::get()->quote($condition->getValues());
                break;

            case "range":
                $values = $condition->getValues();

                $rendered = 'TRIM(`'.$condition->getFieldName().'`) >= '.$values['from'].' AND TRIM(`'.$condition->getFieldName().'`) <= '.$values['to'];
                break;

            case "concat":

                $values = $condition->getValues();
                $conditions = [];

                foreach ($values['conditions'] as $cond) {
                    $conditions[] = $this->renderCondition($cond);
                }

                $rendered = implode($values['operator'], $conditions);


                break;

            default:
                throw new \Exception($condition->getType() . " is not supported yet");
        }

        return $rendered;
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
     * get tablename for relations.
     *
     * @return string
     */
    public function getRelationTablename()
    {
        return 'coreshop_index_mysql_relations_'.$this->getIndex()->getName();
    }

    /**
     * Get System Attributes.
     *
     * @return array
     */
    protected function getSystemAttributes()
    {
        return array('o_id', 'o_classId', 'o_type', 'categoryIds', 'parentCategoryIds', 'active', 'shops');
    }
}
