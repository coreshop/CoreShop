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

use CoreShop\Model\Index;
use CoreShop\Model\Index\Config\Column;
use CoreShop\Model\Product;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Pimcore\Db;
use Pimcore\Logger;
use CoreShop\IndexService\Condition\Elasticsearch as ConditionRenderer;

/**
 * Class Elasticsearch
 * @package CoreShop\IndexService
 */
class Elasticsearch extends AbstractWorker
{
    /**
     * @var string
     */
    public static $type = 'elasticsearch';

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
    public function getElasticsearchClient()
    {
        if (is_null($this->client)) {
            $builder = ClientBuilder::create();
            $builder->setHosts(explode(",", $this->config->getHosts()));
            $this->client = $builder->build();
        }

        return $this->client;
    }

    /**
     * Create Database index table.
     */
    public function createOrUpdateIndexStructures()
    {
        try {
            $params = ['index' => $this->getIndex()->getName()];

            $this->getElasticsearchClient()->indices()->delete($params);
        } catch (\Exception $ex) {
            \Pimcore\Logger::error($ex);
        }

        $result = $this->getElasticsearchClient()->indices()->exists(['index' => $this->getIndex()->getName()]);
        if (!$result) {
            $result = $this->getElasticsearchClient()->indices()->create(['index' => $this->getIndex()->getName(), 'body' => ['settings' => ["number_of_shards" => 5, "number_of_replicas" => 0]]]); //TODO: add to ui
            Logger::info('Creating new Index. Name: ' . $this->getIndex()->getName());
            if (!$result['acknowledged']) {
                throw new \Exception("Index creation failed. IndexName: " . $this->getIndex()->getName());
            }

            //index didn't exist -> reset index queue to make sure all products get reindexed
            //$this->resetIndexingQueue();
        } else {
            $this->getElasticsearchClient()->indices()->delete(['index' => $this->getIndex()->getName()]);
        }

        $properties = [];

        $systemColumns = $this->getSystemAttributes();
        $columnConfig = $this->getColumnsConfiguration();

        foreach($systemColumns as $column => $type) {
            $properties[$column] = array(
                'type' => $this->renderFieldType($type)
            );
        }

        foreach ($columnConfig as $column) {
            if($column instanceof Column) {
                $properties[$column->getName()] = array(
                    'type' => $this->renderFieldType($column->getColumnType())
                );
            }
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
            $this->getElasticsearchClient()->indices()->putMapping($params);
        } catch (\Exception $e) {
            Logger::info($e->getMessage());
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
                Logger::warn('Error during updating index table: '.$e);
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
            Logger::info("Don't adding product ".$object->getId().' to index.');

            $this->deleteFromIndex($object);
        }
    }

    /**
     * Renders a condition to MySql
     *
     * @param Condition $condition
     * @return array
     * @throws \Exception
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
    public function renderFieldType($type) {
        switch($type) {
            case Column::FIELD_TYPE_INTEGER:
                return "integer";

            case Column::FIELD_TYPE_BOOLEAN:
                return "boolean";

            case Column::FIELD_TYPE_DATE:
                return "date";

            case Column::FIELD_TYPE_DOUBLE:
                return "dizbke";

            case Column::FIELD_TYPE_STRING:
                return "text";

            case Column::FIELD_TYPE_TEXT:
                return "text";
        }

        throw new \Exception($type . " is not supported by Elasticsearch Index");
    }

    /**
     * Return Productlist.
     *
     * @return Product\Listing\Elasticsearch
     */
    public function getProductList()
    {
        return new Product\Listing\Elasticsearch($this->getIndex());
    }
}
