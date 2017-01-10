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
use CoreShop\Model\Product;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Pimcore\Db;
use Pimcore\Logger;

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

        foreach ($systemColumns as $column => $type) {
            $properties[$column] = [
                'type' => $type
            ];
        }

        foreach ($columnConfig as $column) {
            $properties[$column->getName()] = [
                'type' => $column->getColumnType()
            ];
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
     * @return string
     * @throws \Exception
     */
    public function renderCondition(Condition $condition)
    {
        switch ($condition->getType()) {

            case "in":
                $rendered = ["terms" => [
                    $condition->getFieldName() => $condition->getValues()
                ]];
                break;

            case "range":
                $values = $condition->getValues();

                $rendered = ["range" => [
                    $condition->getFieldName() => [
                        "gte" => $values['from'],
                        "lte" => $values['to']
                    ]
                ]];
                break;

            case "concat":
                $values = $condition->getValues();
                $rendered = [
                    "filter" => [
                        $values['operator'] => []
                    ]
                ];

                foreach ($values['conditions'] as $cond) {
                    $rendered["filter"][$values['operator']][] = $this->renderCondition($cond);
                }

                break;

            case "like":
                $values = $condition->getValues();

                $pattern = $values["pattern"];
                $value = $values["value"];

                $patternValue = '';

                switch ($pattern) {
                    case "left":
                        $patternValue = '*' . $value;
                        break;
                    case "right":
                        $patternValue = $value . '*';
                        break;
                    case "both":
                        $patternValue = '*' . $value . '*';
                        break;
                }

                $rendered = ["wildcard" => [
                    $condition->getFieldName() => $patternValue
                ]];

                break;

            case "compare":
                $values = $condition->getValues();
                $value = $values['value'];
                $operator = $values['operator'];

                if ($operator === "=" || $operator === "!=") {
                    if ($operator === "!=") {
                        $rendered = ["not" =>
                            [
                                "term" => [
                                    $condition->getFieldName() => $condition->getValues()
                                ]
                            ]
                        ];
                    } else {
                        $rendered = ["term" => [
                            $condition->getFieldName() => $condition->getValues()
                        ]];
                    }
                } else {
                    $map = [
                        ">" => "gt",
                        ">=" => "gte",
                        "<" => "lt",
                        "<=" => "lte"
                    ];

                    if (array_key_exists($operator, $map)) {
                        $rendered = ["range" => [
                            $condition->getFieldName() => [
                                $map[$operator] => $value
                            ]
                        ]];
                    } else {
                        throw new \Exception($operator . " is not supported for compare method");
                    }
                }
                break;

            default:
                throw new \Exception($condition->getType() . " is not supported yet");
        }

        return $rendered;
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

    /**
     * Get System Attributes.
     *
     * @return array
     */
    protected function getSystemAttributes()
    {
        return ['o_id' => "long", 'o_key' => 'string', 'o_classId' => "string", 'o_type' => "string", 'categoryIds' => "long", 'parentCategoryIds' => "long", 'active' => "boolean", 'shops' => "long", 'minPrice' => 'double', 'maxPrice' => 'double'];
    }
}
