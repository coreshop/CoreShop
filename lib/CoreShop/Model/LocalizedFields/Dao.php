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

namespace CoreShop\Model\LocalizedFields;

use CoreShop\Model\Dao\AbstractDao;
use Pimcore\Logger;
use Pimcore\Model;
use Pimcore\Model\Object;
use Pimcore\Tool;

/**
 * Class Dao
 * @package CoreShop\Model\LocalizedFields
 */
class Dao extends AbstractDao
{
    /**
     * @var null
     */
    protected $tableDefinitions = null;

    /**
     * Get Table Name.
     *
     * @return string
     */
    public function getLocalizedTableName()
    {
        return $this->model->getObject()->getTableName().'_data';
    }

    /**
     * Get Query Table Name.
     *
     * @return string
     */
    public function getQueryTableName()
    {
        return $this->model->getObject()->getTableName().'_query';
    }

    /**
     * Save Localized Fields.
     */
    public function save()
    {
        $this->delete(false);

        $object = $this->model->getObject();
        $validLanguages = Tool::getValidLanguages();
        $localizedFields = $this->model->getFields();

        foreach ($validLanguages as $language) {
            $queryTable = $this->getQueryTableName().'_'.$language;
            $sql = 'SELECT * FROM '.$queryTable.' WHERE ooo_id = '.$object->getId()." AND language = '".$language."'";

            try {
                $this->db->fetchRow($sql);
            } catch (\Exception $e) {
                // if the table doesn't exist -> create it!
                if (strpos($e->getMessage(), 'exist')) {

                    // the following is to ensure consistent data and atomic transactions, while having the flexibility
                    // to add new languages on the fly without saving all classes having localized fields

                    // first we need to roll back all modifications, because otherwise they would be implicitly committed
                    // by the following DDL
                    //$this->db->rollBack();

                    // this creates the missing table
                    $this->createUpdateTable();

                    // at this point, we retry to save everything
                    $this->save();

                    return;
                }
            }

            $insertData = [
                'ooo_id' => $this->model->getObject()->getId(),
                'language' => $language,
            ];

            foreach ($localizedFields as $field) {
                $insertData[$field] = $this->model->getLocalizedValue($field, $language, true);
            }

            $this->db->insertOrUpdate($this->getLocalizedTableName(), $insertData);

            // query table
            $data = [];
            $data['ooo_id'] = $this->model->getObject()->getId();
            $data['language'] = $language;

            // get fields which shouldn't be updated
            $untouchable = [];

            foreach ($this->model->getFields() as $key) {
                if (!(in_array($key, $untouchable) && !is_array($this->model->$key))) {
                    $localizedValue = $this->model->getLocalizedValue($key, $language);
                    $insertData = $localizedValue;

                    if (is_array($insertData)) {
                        $data = array_merge($data, $insertData);
                    } else {
                        $data[$key] = $insertData;
                    }
                } else {
                    Logger::debug('Excluding untouchable query value for object [ '.$this->model->getId()." ]  key [ $key ] because it has not been loaded");
                }
            }

            $queryTable = $this->getQueryTableName().'_'.$language;
            $this->db->insertOrUpdate($queryTable, $data);
        } // foreach language
    }

    /**
     * Delete Localized Fields.
     *
     * @param bool $deleteQuery
     */
    public function delete($deleteQuery = true)
    {
        try {
            if ($deleteQuery) {
                $this->db->delete($this->getLocalizedTableName(), $this->db->quoteInto('ooo_id = ?', $this->model->getObject()->getId()));

                $validLanguages = Tool::getValidLanguages();
                foreach ($validLanguages as $language) {
                    $queryTable = $this->getQueryTableName().'_'.$language;
                    $this->db->delete($queryTable, $this->db->quoteInto('ooo_id = ?', $this->model->getObject()->getId()));
                }
            }
        } catch (\Exception $e) {
            Logger::error($e);
            $this->createUpdateTable();
        }
    }

    /**
     *
     */
    public function load()
    {
        $validLanguages = Tool::getValidLanguages();
        foreach ($validLanguages as &$language) {
            $language = $this->db->quote($language);
        }

        $data = $this->db->fetchAll('SELECT * FROM '.$this->getLocalizedTableName().' WHERE ooo_id = ? AND language IN ('.implode(',', $validLanguages).')', $this->model->getObject()->getId());

        foreach ($data as $row) {
            foreach ($this->model->getFields() as $field) {
                $this->model->setLocalizedValue($field, $row[$field], $row['language']);
            }
        }
    }

    /**
     * Create Localized Views.
     */
    public function createLocalizedViews()
    {

        // init
        $languages = Tool::getValidLanguages();

        $db = $this->db;

        /*
         * macro for creating ifnull statement
         * @param string $field
         * @param array  $languages
         *
         * @return string
         */
        $getFallbackValue = function ($field, array $languages) use (&$getFallbackValue, $db) {

            // init
            $lang = array_shift($languages);

            // get fallback for current language
            $fallback = count($languages) > 0
                ? $getFallbackValue($field, $languages)
                : 'null'
            ;

            // create query
            $sql = sprintf('ifnull(`%s`.`%s`, %s)', $lang, $field, $fallback
            );

            return $fallback !== 'null'
                ? $sql
                : $db->quoteIdentifier($lang).'.'.$db->quoteIdentifier($field)
                ;
        };

        foreach ($languages as $language) {
            try {
                $tablename = $this->getQueryTableName().'_'.$language;

                // get available columns
                $viewColumns = array_merge(
                    $this->db->fetchAll('SHOW COLUMNS FROM `'.$this->model->getObject()->getTableName().'`')
                );
                $localizedColumns = $this->db->fetchAll('SHOW COLUMNS FROM `'.$tablename.'`');

                // get view fields
                $viewFields = [];
                foreach ($viewColumns as $row) {
                    $viewFields[] = $this->db->quoteIdentifier($row['Field']);
                }

                // create fallback select
                $localizedFields = [];
                $fallbackLanguages = array_unique(Tool::getFallbackLanguagesFor($language));
                array_unshift($fallbackLanguages, $language);
                foreach ($localizedColumns as $row) {
                    $localizedFields[] = $getFallbackValue($row['Field'], $fallbackLanguages).sprintf(' as "%s"', $row['Field']);
                }

                // create view select fields
                $selectViewFields = implode(',', array_merge($viewFields, $localizedFields));
                $localizedTable = $this->getLocalizedTableName().'_localized';

                // create view
                $viewQuery = <<<QUERY
CREATE OR REPLACE VIEW `{$localizedTable}_{$language}` AS

SELECT {$selectViewFields}
FROM `{$this->model->getObject()->getTableName()}`
QUERY;

                // join fallback languages
                foreach ($fallbackLanguages as $lang) {
                    $viewQuery .= <<<QUERY
LEFT JOIN {$this->getQueryTableName()}_{$lang} as {$lang}
    ON( 1
        AND {$this->model->getObject()->getTableName()}.id = {$lang}.ooo_id
    )
QUERY;
                }

                // execute
                $this->db->query($viewQuery);
            } catch (\Exception $e) {
                Logger::error($e);
            }
        }
    }

    /**
     * Create or Update Localized Table.
     */
    public function createUpdateTable()
    {
        $table = $this->getLocalizedTableName();

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table."` (
		  `ooo_id` int(11) NOT NULL default '0',
		  `language` varchar(10) NOT NULL DEFAULT '',
		  PRIMARY KEY (`ooo_id`,`language`),
          INDEX `ooo_id` (`ooo_id`),
          INDEX `language` (`language`)
		) DEFAULT CHARSET=utf8;");

        $existingColumns = $this->getValidTableColumns($table, false); // no caching of table definition
        $columnsToRemove = $existingColumns;
        $protectedColumns = ['ooo_id', 'language'];

        foreach ($this->model->getFields() as $field) {
            $this->addModifyColumn($table, $field, 'varchar(255)', '', 'NULL');
            $protectedColumns[] = $field;
        }

        $this->removeUnusedColumns($table, $columnsToRemove, $protectedColumns);

        $validLanguages = Tool::getValidLanguages();

        foreach ($validLanguages as &$language) {
            $queryTable = $this->getQueryTableName();
            $queryTable .= '_'.$language;

            $this->db->query('CREATE TABLE IF NOT EXISTS `'.$queryTable."` (
                  `ooo_id` int(11) NOT NULL default '0',
                  `language` varchar(10) NOT NULL DEFAULT '',
                  PRIMARY KEY (`ooo_id`,`language`),
                  INDEX `ooo_id` (`ooo_id`),
                  INDEX `language` (`language`)
                ) DEFAULT CHARSET=utf8;");

            // create object table if not exists
            $protectedColumns = ['ooo_id', 'language'];

            $existingColumns = $this->getValidTableColumns($queryTable, false); // no caching of table definition
            $columnsToRemove = $existingColumns;

            foreach ($this->model->getFields() as $field) {
                $this->addModifyColumn($queryTable, $field, 'varchar(255)', '', 'NULL');
                $protectedColumns[] = $field;
            }

            // remove unused columns in the table
            $this->removeUnusedColumns($queryTable, $columnsToRemove, $protectedColumns);
        }

        $this->createLocalizedViews();

        $this->tableDefinitions = null;
    }

    /**
     * Add/Modify Column.
     *
     * @param $table
     * @param $colName
     * @param $type
     * @param $default
     * @param $null
     */
    private function addModifyColumn($table, $colName, $type, $default, $null)
    {
        $existingColumns = $this->getValidTableColumns($table, false);
        $existingColName = null;

        // check for existing column case insensitive eg a rename from myInput to myinput
        $matchingExisting = preg_grep('/^'.preg_quote($colName, '/').'$/i', $existingColumns);
        if (is_array($matchingExisting) && !empty($matchingExisting)) {
            $existingColName = current($matchingExisting);
        }

        if ($existingColName === null) {
            $this->db->query('ALTER TABLE `'.$table.'` ADD COLUMN `'.$colName.'` '.$type.$default.' '.$null.';');
            $this->resetValidTableColumnsCache($table);
        } else {
            if (!Object\ClassDefinition\Service::skipColumn($this->tableDefinitions, $table, $colName, $type, $default, $null)) {
                $this->db->query('ALTER TABLE `'.$table.'` CHANGE COLUMN `'.$existingColName.'` `'.$colName.'` '.$type.$default.' '.$null.';');
            }
        }
    }

    /**
     * Remove Unused Column.
     *
     * @param $table
     * @param $columnsToRemove
     * @param $protectedColumns
     */
    private function removeUnusedColumns($table, $columnsToRemove, $protectedColumns)
    {
        if (is_array($columnsToRemove) && count($columnsToRemove) > 0) {
            foreach ($columnsToRemove as $value) {
                if (!in_array(strtolower($value), array_map('strtolower', $protectedColumns))) {
                    $this->db->query('ALTER TABLE `'.$table.'` DROP COLUMN `'.$value.'`;');
                }
            }
        }
    }
}
