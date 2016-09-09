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
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Pimcore\Db;

/**
 * Class Elasticsearch
 * @package CoreShop\IndexService
 */
class Elasticsearch extends AbstractWorker
{
    /**
     * Database.
     *
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $db;

    /**
     * @var Index\Config\Elasticsearch
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Mysql constructor.
     *
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        parent::__construct($index);

        $this->db = Db::get();
        $this->config = $index->getConfig();
    }

    /**
     * @return Client
     */
    protected function getElasticsearchClient() {
        if(is_null($this->client)) {
            $builder = ClientBuilder::create();
            $builder->setHosts(explode(",", $this->config->getHosts()));
            $this->client = $builder->build();
        }

        return $this->client;
    }

    protected function createTableStructure() {
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
    }

    /**
     * Create Database index table.
     */
    public function createOrUpdateIndexStructures()
    {
        try {
            $params = ['index' => $this->getIndex()->getName()];

            $this->getElasticsearchClient()->indices()->delete($params);
        }
        catch(\Exception $ex) {

        }

        $result = $this->getElasticsearchClient()->indices()->exists(['index' => $this->getIndex()->getName()]);
        if(!$result) {
            $result = $this->getElasticsearchClient()->indices()->create(['index' => $this->getIndex()->getName(), 'body' => ['settings' => ["number_of_shards" => 5, "number_of_replicas" => 0]]]); //TODO: add to ui
            \Logger::info('Creating new Index. Name: ' . $this->getIndex()->getName());
            if(!$result['acknowledged']) {
                throw new \Exception("Index creation failed. IndexName: " . $this->getIndex()->getName());
            }

            //index didn't exist -> reset index queue to make sure all products get reindexed
            //$this->resetIndexingQueue();
        }


        $properties = [];

        $systemColumns = $this->getSystemAttributes();
        $columnConfig = $this->getColumnsConfiguration();

        foreach($systemColumns as $column => $type) {
            $properties[$column] = array(
                'type' => $type
            );
        }

        foreach ($columnConfig as $column) {
            $properties[$column->getName()] = array(
                'type' => $column->getColumnType()
            );
        }

        $params = [
            'index' => $this->getIndex()->getName(),
            'type' => "coreshop",
            'body'  => [
                'coreshop' => [
                    'properties' => $properties
                ]
            ]
        ];

        try {
            $result = $this->getElasticsearchClient()->indices()->putMapping($params);
        } catch(\Exception $e) {
            \Logger::info($e->getMessage());

        }
    }

    /**
     * deletes necessary index structuers (like database tables).
     *
     * @return mixed
     */
    public function deleteIndexStructures()
    {
        $this->getElasticsearchClient()->indices()->delete([
            'index' => $this->getIndex()->getName()
        ]);
    }

    /**
     * Delete Product from index.
     *
     * @param Product $object
     */
    public function deleteFromIndex(Product $object)
    {
        $params = [
            'index' => $this->getIndex()->getName(),
            'type' => 'coreshop',
            'id' => $object->getId()
        ];

        $this->getElasticsearchClient()->delete($params);
    }

    /**
     * Update or create product in index.
     *
     * @param Product $object
     */
    public function updateIndex(Product $object)
    {
        if ($object->getDoIndex()) {
            $preparedData = $this->prepareData($object, false);

            try {
                $params = [
                    'index' => $this->getIndex()->getName(),
                    'type' => 'coreshop',
                    'id' => $object->getId(),
                    'body' => $preparedData['data']
                ];


                $this->getElasticsearchClient()->index($params);
            } catch (\Exception $e) {
                \Logger::warn('Error during updating index table: '.$e);
            }

            /*try {
                $this->db->delete($this->getRelationTablename(), 'src = '.$this->db->quote($object->getId()));
                foreach ($preparedData['relation'] as $rd) {
                    $this->db->insert($this->getRelationTablename(), $rd);
                }
            } catch (\Exception $e) {
                \Logger::warn('Error during updating index relation table: '.$e->getMessage(), $e);
            }*/
        } else {
            \Logger::info("Don't adding product ".$object->getId().' to index.');

            $this->deleteFromIndex($object);
        }
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
        return array('o_id' => "long", 'o_classId' => "string", 'o_type' => "string", 'categoryIds' => "long", 'parentCategoryIds' => "long", 'active' => "boolean", 'shops' => "long");
    }
}
