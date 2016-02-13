<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\IndexService;

use CoreShop\Model\Index;
use CoreShop\Model\Product;
use Pimcore\Tool;

class Mysql extends AbstractWorker
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $db;

    /**
     * Mysql constructor.
     */
    public function __construct(Index $index) {
        parent::__construct($index);

        $this->db = \Pimcore\Db::get();
    }

    /**
     * Create Database index table
     */
    public function createOrUpdateIndexStructures()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->getTablename() . "` (
          `o_id` int(11) NOT NULL default '0',
          `o_virtualProductId` int(11) NOT NULL,
          `o_virtualProductActive` TINYINT(1) NOT NULL,
          `o_classId` int(11) NOT NULL,
          `o_type` varchar(20) NOT NULL,
          `categoryIds` varchar(255) NOT NULL,
          `active` TINYINT(1) NOT NULL,
          PRIMARY KEY  (`o_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $data = $this->db->fetchAll("SHOW COLUMNS FROM " . $this->getTablename());
        $columns = array();

        foreach ($data as $d) {
            $columns[$d["Field"]] = $d["Field"];
        }

        $systemColumns = $this->getSystemAttributes();
        $columnsToDelete = $columns;
        $columnsToAdd = array();

        //TODO: Load Configuration from anywhere (eg Configfile or Database)
        $columnConfig = $this->getColumnsConfiguration();

        if($columnConfig) {
            foreach($columnConfig as $column) {
                if(!array_key_exists($column['config']['name'], $columns)) {

                    $doAdd = true;

                    //TODO: Did not yet understood interpreter, maybe implement it anytime?
                    /*if(!empty($column->interpreter)) {
                        $interpreter = $column->interpreter;
                        $interpreterObject = new $interpreter();
                        if($interpreterObject instanceof OnlineShop_Framework_IndexService_RelationInterpreter) {
                            $doAdd = false;
                        }
                    }*/

                    if($doAdd) {
                        $columnsToAdd[$column['config']['name']] = $column['config']['type'];
                    }
                }

                unset($columnsToDelete[$column['config']['name']]);
            }
        }

        foreach($columnsToDelete as $c)
        {
            if(!in_array($c, $systemColumns))
            {
                $this->db->query('ALTER TABLE `' . $this->getTablename() . '` DROP COLUMN `' . $c . '`;');
            }
        }


        foreach($columnsToAdd as $c => $type) {
            $this->db->query('ALTER TABLE `' . $this->getTablename() . '` ADD `' . $c . '` ' . $type . ';');
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->getRelationTablename() . "` (
          `src` int(11) NOT NULL default '0',
          `src_virtualProductId` int(11) NOT NULL,
          `dest` int(11) NOT NULL,
          `fieldname` varchar(255) COLLATE utf8_bin NOT NULL,
          `type` varchar(20) COLLATE utf8_bin NOT NULL,
          PRIMARY KEY (`src`,`dest`,`fieldname`,`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
    }

    /**
     * Delete Product from index
     *
     * @param Product $object
     */
    public function deleteFromIndex(Product $object)
    {
        $this->db->delete($this->getTablename(), "o_id = " . $this->db->quote($object->getId()));
        $this->db->delete($this->getRelationTablename(), "src = " . $this->db->quote($object->getId()));
    }

    /**
     * Update or create product in index
     *
     * @param Product $object
     */
    public function updateIndex(Product $object)
    {
        if($object->getDoIndex()) {
            $a = \Pimcore::inAdmin();
            $b = \Pimcore\Model\Object\AbstractObject::doGetInheritedValues();
            \Pimcore::unsetAdminMode();
            \Pimcore\Model\Object\AbstractObject::setGetInheritedValues(true);
            $hidePublishedMemory = \Pimcore\Model\Object\AbstractObject::doHideUnpublished();
            \Pimcore\Model\Object\AbstractObject::setHideUnpublished(false);

            $categories = $object->getCategories();

            $categoryIds = array();

            if($categories) {
                foreach($categories as $c) {
                    $categoryIds[$c->getId()] = $c->getId();
                }
            }

            ksort($categoryIds);

            $virtualProductId = $object->getId();
            $virtualProductActive = $object->getEnabled();

            if($object->getType() === Product::OBJECT_TYPE_VARIANT) {
                $parent = $object->getParent();

                while($parent->getType() === Product::OBJECT_TYPE_VARIANT && $parent instanceof Product) {
                    $parent = $parent->getParent();
                }

                $virtualProductId = $parent->getId();
                $virtualProductActive = $parent->getEnabled();
            }

            $data = array(
                "o_id" => $object->getId(),
                "o_classId" => $object->getClassId(),
                "o_virtualProductId" => $virtualProductId,
                "o_virtualProductActive" => $virtualProductActive,
                "o_type" => $object->getType(),
                "categoryIds" => ',' . implode(",", $categoryIds) . ",",
                "active" => $object->getEnabled()
            );

            $relationData = array();
            $columnConfig = $this->getColumnsConfiguration();

            foreach($columnConfig as $column) {
                try {
                    $value = null;

                    if(!empty($column['config']['getter']))
                    {
                        $getter = $column['config']['getter'];

                        $getterClass = "\\CoreShop\\IndexService\\Getter\\" . $getter;

                        if(Tool::classExists($getterClass)) {
                            $value = $getterClass::get($object, $column);
                        }
                    }
                    else
                    {
                        $getter = "get" . ucfirst($column['key']);

                        if(method_exists($object, $getter))
                        {
                            $value = $object->$getter($column['config']['locale']);
                        }
                    }

                    $data[$column['config']['name']] = $value;

                    if(is_array($data[$column['config']['name']])) {
                        $data[$column['config']['name']] = "," . implode($data[$column['config']['name']], ",") . ",";
                    }

                } catch(\Exception $e) {
                    \Logger::err("Exception in CoreShopIndexService: " . $e->getMessage(), $e);
                }

            }

            if($a) {
                \Pimcore::setAdminMode();
            }
            \Pimcore\Model\Object\AbstractObject::setGetInheritedValues($b);
            \Pimcore\Model\Object\AbstractObject::setHideUnpublished($hidePublishedMemory);

            try {

                $this->doInsertData($data);

            } catch (\Exception $e) {
                \Logger::warn("Error during updating index table: " . $e);
            }

        }
        else
        {
            \Logger::info("Don't adding product " . $object->getId() . " to index.");

            try {
                $this->db->delete($this->getTablename(), "o_id = " . $this->db->quote($object->getId()));
            } catch (\Exception $e) {
                \Logger::warn("Error during updating index table: " . $e->getMessage(), $e);
            }

            try {
                $this->db->delete($this->getRelationTablename(), "src = " . $this->db->quote($object->getId()));
            } catch (\Exception $e) {
                \Logger::warn("Error during updating index relation table: " . $e->getMessage(), $e);
            }
        }
    }

    /**
     * @param $data
     */
    protected function doInsertData($data) {
        //insert index data
        $dataKeys = [];
        $updateData = [];
        $insertData = [];
        $insertStatement = [];

        foreach($data as $key => $d) {
            $dataKeys[$this->db->quoteIdentifier($key)] = '?';
            $updateData[] = $d;
            $insertStatement[] = $this->db->quoteIdentifier($key) . " = ?";
            $insertData[] = $d;
        }

        $insert = "INSERT INTO " . $this->getTablename() . " (" . implode(",", array_keys($dataKeys)) . ") VALUES (" . implode("," , $dataKeys) . ")"
            . " ON DUPLICATE KEY UPDATE " . implode(",", $insertStatement);

        $this->db->query($insert, array_merge($updateData, $insertData));
    }

    /**
     * @return array
     */
    public function getColumnsConfiguration() {
        $config = $this->index->getConfig();

        return $config['columns'];
    }

    /**
     * @return array
     */
    public function getFulltextSearchColumns() {
        //TODO: Load from configurations (eg configfile or database)
        return array("name");
    }

    /**
     * @return Product\Listing\Mysql
     */
    public function getProductList() {
        return new Product\Listing\Mysql();
    }

    /**
     * get table name
     *
     * @return string
     */
    protected function getTablename() {
        return "coreshop_index_mysql_" . $this->getIndex()->getName();
    }

    /**
     * get tablename for relations
     *
     * @return string
     */
    protected function getRelationTablename() {
        return "coreshop_index_mysql_relations_" . $this->getIndex()->getName();
    }

    /**
     * @return array
     */
    protected function getSystemAttributes() {
        return array("o_id", "o_classId", "o_virtualProductId", "o_virtualProductActive", "o_type", "categoryIds", "active");
    }
}