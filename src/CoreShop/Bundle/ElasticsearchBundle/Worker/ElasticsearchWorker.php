<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ElasticsearchBundle\Worker;

use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Extension\IndexColumnTypeConfigExtension;
use CoreShop\Component\Index\Extension\IndexRelationalColumnsExtensionInterface;
use CoreShop\Component\Index\Extension\IndexSystemColumnTypeConfigExtension;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderRendererInterface;
use CoreShop\Component\Index\Worker\FilterGroupHelperInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Db\Connection;
use Pimcore\Log\Simple;
use Pimcore\Tool;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticsearchWorker extends AbstractWorker
{
    protected Client $client;

    public function __construct(
        ServiceRegistryInterface $extensionsRegistry,
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry,
        FilterGroupHelperInterface $filterGroupHelper,
        ConditionRendererInterface $conditionRenderer,
        OrderRendererInterface $orderRenderer,
        protected Connection $database
    ) {
        parent::__construct(
            $extensionsRegistry,
            $getterServiceRegistry,
            $interpreterServiceRegistry,
            $filterGroupHelper,
            $conditionRenderer,
            $orderRenderer
        );

        $builder = ClientBuilder::create();
        $builder->setHosts(['http://elasticsearch:9200']);
        $this->client = $builder->build();
    }

    /**
     * @return Client
     */
    public function getElasticsearchClient()
    {
        /*if (is_null($this->client)) {
            $builder = ClientBuilder::create();
            $builder->setHosts(explode(",", $this->config->getHosts()));
            $this->client = $builder->build();
        }*/

        return $this->client;
    }

    public function createOrUpdateIndexStructures(IndexInterface $index): void
    {
        $tableName = $this->getTablename($index->getName());
        $localizedTableName = $this->getLocalizedTablename($index->getName());
        $relationalTableName = $this->getRelationTablename($index->getName());

        $this->createTableSchema($index, $tableName);
        $this->createLocalizedTableSchema($index, $localizedTableName);
        $this->createRelationalTableSchema($index, $relationalTableName);
    }

    protected function createTableSchema(IndexInterface $index, string $tableName)
    {
        try {
            $this->truncateOrCreateTable($tableName);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }

        $properties = [];

        foreach ($index->getColumns() as $column) {
            if ($column instanceof IndexColumnInterface) {
                $type = $column->getObjectType();
                $interpreterClass = $column->hasInterpreter() ? $this->getInterpreterObject($column) : null;
                if ($type !== 'localizedfields' && !$interpreterClass instanceof LocalizedInterpreterInterface) {
                    if (in_array($column->getDataType(), ['manyToOneRelation', 'manyToManyObjectRelation']) &&
                        in_array($column->getColumnType(), ['STRING', 'TEXT'])) {
                        $properties[$column->getName()] = [
                            'type' => 'keyword'
                        ];

                        continue;
                    }

                    $properties[$column->getName()] = [
                        'type' => $this->renderFieldType($column->getColumnType())
                    ];
                }
            }
        }

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getSystemColumns() as $name => $type) {
                    if (in_array($name, ['categoryIds', 'stores', 'parentCategoryIds'])) {
                        $properties[$name] = [
                            'type' => 'keyword'
                        ];

                        continue;
                    }

                    $properties[$name] = [
                        'type' => $this->renderFieldType($type)
                    ];
                }
            }
        }

        foreach ($this->getSystemAttributes() as $column => $type) {
            $properties[$column] = [
                'type' => $this->renderFieldType($type)
            ];
        }

        Simple::log('elastic-worker', serialize($properties));

        $this->mapTableProperties($tableName, $properties);
    }

    protected function createLocalizedTableSchema(IndexInterface $index, string $tableName)
    {
        try {
            $this->truncateOrCreateTable($tableName);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }

        $properties = [];

        foreach ($index->getColumns() as $column) {
            if ($column instanceof IndexColumnInterface) {
                $type = $column->getObjectType();
                $interpreterClass = $column->hasInterpreter() ? $this->getInterpreterObject($column) : null;
                if ($type === 'localizedfields' || $interpreterClass instanceof LocalizedInterpreterInterface) {
                    $properties[$column->getName()] = [
                        'type' => $this->renderFieldType($column->getColumnType())
                    ];
                }
            }
        }

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getLocalizedSystemColumns() as $name => $type) {
                    $config = ['notnull' => false];

                    //TODO check what this is
                    if ($extension instanceof IndexSystemColumnTypeConfigExtension) {
                        $config = array_merge($config, $extension->getSystemColumnConfig($name, $type));
                    }

                    $properties[$name] = [
                        'type' => $this->renderFieldType($type)
                    ];
                }
            }
        }

        foreach ($this->getLocalizedSystemAttributes() as $column => $type) {
            $properties[$column] = [
                'type' => $this->renderFieldType($type)
            ];
        }

        $this->mapTableProperties($tableName, $properties);
    }

    protected function createRelationalTableSchema(IndexInterface $index, string $tableName)
    {
        try {
            $this->truncateOrCreateTable($tableName);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }

        $properties = [];

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexRelationalColumnsExtensionInterface) {
                foreach ($extension->getRelationalColumns() as $name => $type) {
                    $config = ['notnull' => false];

                    //TODO see what config is
                    if ($extension instanceof IndexSystemColumnTypeConfigExtension) {
                        $config = array_merge($config, $extension->getSystemColumnConfig($name, $type));
                    }

                    $properties[$name] = [
                        'type' => $this->renderFieldType($type)
                    ];
                }
            }
        }

        foreach ($this->getRelationalSystemAttributes() as $column => $type) {
            $properties[$column] = [
                'type' => $this->renderFieldType($type)
            ];
        }

        Simple::log('elastic-worker', serialize($properties));

        $this->mapTableProperties($tableName, $properties);
    }

    protected function createLocalizedViews(IndexInterface $index)
    {
        $queries = [];
        $languages = Tool::getValidLanguages(); //TODO: Use Locale Service

        foreach ($languages as $language) {
            $localizedTable = $this->getLocalizedTablename($index->getName());
            $localizedViewName = $this->getLocalizedViewName($index->getName(), $language);
            $tableName = $this->getTableName($index->getName());

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

    protected function truncateOrCreateTable(string $tableName)
    {
        $params = ['index' => $tableName];

        try {
            $this->client->indices()->delete($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }

        try {
            $result = $this->client->indices()->exists($params);
        } catch (\Exception $e) {
            $result = null;
            Simple::log('elastic-worker', (string)$e);
        }

        if (!$result) {
            $result = $this->client->indices()->create(
                [
                    'index' => $tableName,
                    'body' => [
                        'settings' => [
                            "number_of_shards" => 5,
                            "number_of_replicas" => 0
                        ]
                    ]
                ]
            );

            Simple::log('elastic-worker','Creating new Index. Name: ' . $tableName);

            if (!$result['acknowledged']) {
                throw new \Exception("Index creation failed. IndexName: " . $tableName);
            }
        } else {
            try {
                $this->client->indices()->delete($params);
            } catch (\Exception $e) {
                Simple::log('elastic-worker', (string)$e);
            }
        }
    }

    protected function mapTableProperties(string $tableName, array $properties)
    {
        $params = [
            'index' => $tableName,
            'type' => "coreshop",
            'include_type_name' => true,
            'body'  => [
                'coreshop' => [
                    'properties' => $properties
                ]
            ]
        ];

        try {
            $this->client->indices()->putMapping($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
    }

    protected function typeCastValues(IndexColumnInterface $column, $value)
    {
        return $value;
    }

    protected function handleArrayValues(IndexInterface $index, array $value)
    {
        return ',' . implode(',', $value) . ',';
    }

    public function deleteIndexStructures(IndexInterface $index)
    {
        try {
            $this->client->indices()->delete([
                'index' => $this->getTablename($index->getName())
            ]);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
    }

    public function renameIndexStructures(IndexInterface $index, string $oldName, string $newName): void
    {
        try {
            $languages = Tool::getValidLanguages();
            $potentialTables = [
                $this->getTablename($oldName) => $this->getTablename($newName),
                $this->getLocalizedTablename($oldName) => $this->getLocalizedTablename($newName),
                $this->getRelationTablename($oldName) => $this->getRelationTablename($newName),
            ];

            foreach ($languages as $language) {
                $potentialTables[$this->getLocalizedViewName($oldName, $language)] = $this->getLocalizedViewName($newName, $language);
            }

            foreach ($potentialTables as $oldTable => $newTable) {
                $result = $this->client->indices()->exists(['index' => $oldTable]);
                if ($result) {
                    $params['body'] = [
                        'source' => [
                            'index' => $oldTable
                        ],
                        'dest' => [
                            'index' => $newTable
                        ]
                    ];

                    $this->client->reindex($params);
                    $this->client->indices()->delete([
                        'index' => $oldTable
                    ]);
                }
            }
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
    }

    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $params = [
            'index' => $this->getTablename($index->getName()),
            'type' => 'coreshop',
            'id' => $object->getId()
        ];

        try {
            $this->client->delete($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
    }

    public function deleteFromRelationalIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $params = [
            'index' => $this->getRelationTablename($index->getName()),
            'type' => 'coreshop',
            'id' => $object->getId()
        ];

        try {
            $this->client->delete($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', (string)$e);
        }
    }

    public function updateIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $doIndex = $object->getIndexable($index);

        if ($doIndex) {
            $preparedData = $this->prepareData($index, $object);

            $this->doInsertData($this->getTablename($index->getName()), $preparedData['data'], $object);

            $this->doInsertData($this->getLocalizedTablename($index->getName()), $preparedData['localizedData'], $object);

            $this->deleteFromRelationalIndex($index, $object);

            if (!empty($preparedData['relation'])) {
                foreach ($preparedData['relation'] as $relationRow) {
                    $this->doInsertData($this->getRelationTablename($index->getName()), $relationRow, $object);
                }
            }

        } else {
            $this->logger->info('Don\'t adding object ' . $object->getId() . ' to index.');

            $this->deleteFromIndex($index, $object);
        }
    }

    protected function doInsertData(string $tableName, array $data, IndexableInterface $object): void
    {
        $params = [
            'index' => $tableName,
            'type' => 'coreshop',
            'id' => $object->getId(),
            'body' => $data
        ];

        try {
            $this->client->index($params);
        } catch (\Exception $e) {
            Simple::log('elastic-worker', 'Error during updating index table: '.$e);
        }
    }

    public function renderFieldType(string $type)
    {
        switch ($type) {
            case IndexColumnInterface::FIELD_TYPE_INTEGER:
                return "integer";

            case IndexColumnInterface::FIELD_TYPE_BOOLEAN:
                return "boolean";

            case IndexColumnInterface::FIELD_TYPE_DATE:
                return "date";

            case IndexColumnInterface::FIELD_TYPE_DOUBLE:
                return "dizbke";

            case IndexColumnInterface::FIELD_TYPE_STRING:
                return "keyword";

            case IndexColumnInterface::FIELD_TYPE_TEXT:
                return "keyword"; //TODO see
        }

        throw new \Exception($type . " is not supported by Elasticsearch Index");
    }

    public function getFieldTypeConfig(IndexColumnInterface $column)
    {
        $config = ['notnull' => false];

        foreach ($this->getExtensions($column->getIndex()) as $extension) {
            if ($extension instanceof IndexColumnTypeConfigExtension) {
                $config = array_merge($config, $extension->getColumnConfig($column));
            }
        }

        return $config;
    }

    public function getSystemFieldTypeConfig(IndexInterface $index, string $name, string $type)
    {
        $config = ['notnull' => false];

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexSystemColumnTypeConfigExtension) {
                $config = array_merge($config, $extension->getSystemColumnConfig($name, $type));
            }
        }

        return $config;
    }

    public function getList(IndexInterface $index): ListingInterface
    {
        return new ElasticsearchWorker\Listing($index, $this, $this->database);
    }

    public function getTablename(string $name): string
    {
        return 'coreshop_index_elasticsearch_' . strtolower($name);
    }

    public function getLocalizedTablename(string $name): string
    {
        return 'coreshop_index_elasticsearch_localized_' . strtolower($name);
    }

    public function getLocalizedViewName(string $name, string $language): string
    {
        return $this->getLocalizedTablename($name) . '_' . $language;
    }

    public function getRelationTablename(string $name): string
    {
        return 'coreshop_index_elasticsearch_relations_' . strtolower($name);
    }
}
