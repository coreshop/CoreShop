<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Bundle\IndexBundle\Worker\OpenSearchWorker\Listing;
use CoreShop\Bundle\IndexBundle\Worker\OpenSearchWorker\Mapping\LanguageAnalyzer;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderRendererInterface;
use CoreShop\Component\Index\Worker\FilterGroupHelperInterface;
use CoreShop\Component\Index\Worker\OpenSearchWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerDeleteableByIdInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\SearchClient\SearchClientInterface;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class OpenSearchWorker extends AbstractWorker implements OpenSearchWorkerInterface, WorkerDeleteableByIdInterface
{
    public function __construct(
        ServiceRegistryInterface $extensions,
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry,
        FilterGroupHelperInterface $filterGroupHelper,
        ConditionRendererInterface $conditionRenderer,
        OrderRendererInterface $orderRenderer,
        private EventDispatcherInterface $eventDispatcher,
        private ServiceRegistryInterface $clientRegistry,
        private ServiceRegistryInterface $interpreterRegistry,
        private SluggerInterface $slugger,
    ) {
        parent::__construct(
            $extensions,
            $getterServiceRegistry,
            $interpreterServiceRegistry,
            $filterGroupHelper,
            $conditionRenderer,
            $orderRenderer,
        );
    }

    /**
     * @inheritDoc
     */
    public function createOrUpdateIndexStructures(IndexInterface $index): void
    {
        $client = $this->getClient($index);
        $indexName = $this->getIndexName($index->getName());

        if ($client->existsIndex(['index' => $indexName])) {
            $this->deleteIndexStructures($index);
        }

        $config = $index->getConfiguration();
        $columns = $this->getIndexColumns($index);

        $columns['_relations'] = [
            'type' => OpenSearchWorkerInterface::FIELD_TYPE_NESTED,
        ];

        $body = [
            'settings' => [
                'index' => [
                    'number_of_shards' => $config['numberOfShards'] ?? 1,
                    'number_of_replicas' => $config['numberOfReplicas'] ?? 1,
                ],
            ],
            'mappings' => [
                'properties' => $columns,
            ],
        ];

        $event = new GenericEvent($index);
        $event->setArguments([
            'index' => $indexName,
            'body' => $body,
        ]);

        $this->eventDispatcher->dispatch($event, 'coreshop.index.create_or_update_index_structures');

        $client->createIndex([
            'index' => $event->getArgument('index'),
            'body' => $event->getArgument('body'),
        ]);
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function deleteIndexStructures(IndexInterface $index): void
    {
        $client = $this->getClient($index);
        $indexName = $this->getIndexName($index->getName());

        if (!$client->existsIndex(['index' => $indexName])) {
            return;
        }

        $client->deleteIndex([
            'index' => $indexName,
        ]);
    }

    public function renameIndexStructures(IndexInterface $index, string $oldName, string $newName): void
    {
        $client = $this->getClient($index);
        $oldIndex = $this->getIndexName($oldName);
        $newIndex = $this->getIndexName($newName);

        // First, check if the old index exists
        if (!$client->existsIndex(['index' => $oldIndex])) {
            return;
        }

        // Re-create the index under the new name with the settings and mappings of the old
        // one. The generic search client abstraction has no "clone" operation, so settings
        // and mappings are copied explicitly and the documents are re-indexed.
        $mappings = $client->getIndexMapping(['index' => $oldIndex])[$oldIndex]['mappings'] ?? [];
        $settings = $client->getIndexSettings(['index' => $oldIndex])[$oldIndex]['settings']['index'] ?? [];

        // Remove internal settings which cannot be set on index creation
        unset(
            $settings['creation_date'],
            $settings['provided_name'],
            $settings['uuid'],
            $settings['version'],
        );

        $client->createIndex([
            'index' => $newIndex,
            'body' => [
                'settings' => ['index' => $settings],
                'mappings' => $mappings,
            ],
        ]);

        $client->reIndex([
            'wait_for_completion' => true,
            'refresh' => true,
            'body' => [
                'source' => ['index' => $oldIndex],
                'dest' => ['index' => $newIndex],
            ],
        ]);

        // Finally, delete the old index
        $client->deleteIndex([
            'index' => $oldIndex,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function deleteFromIndexById(IndexInterface $index, int $id): void
    {
        $client = $this->getClient($index);
        $indexName = $this->getIndexName($index->getName());

        if (!$client->exists(['index' => $indexName, 'id' => (string) $id])) {
            return;
        }

        $client->delete([
            'index' => $indexName,
            'id' => (string) $id,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function deleteFromIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $id = $object->getId();

        if (!is_int($id)) {
            return;
        }

        $this->deleteFromIndexById($index, $id);
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function updateIndex(IndexInterface $index, IndexableInterface $object): void
    {
        $client = $this->getClient($index);
        $indexName = $this->getIndexName($index->getName());
        $objectId = (string) $object->getId();

        // Delete the document from the index if it is not indexable
        if (!$object->getIndexable($index)) {
            if ($client->exists(['index' => $indexName, 'id' => $objectId])) {
                $client->delete([
                    'index' => $indexName,
                    'id' => $objectId,
                ]);
            }

            return;
        }

        $client
            ->index([
                'index' => $indexName,
                'id' => $objectId,
                'body' => $this->prepareDataForOpenSearch($index, $object),
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function renderFieldType($type): string
    {
        return $type;
    }

    /**
     * @inheritDoc
     */
    public function getList(IndexInterface $index): ListingInterface
    {
        return new Listing($index, $this);
    }

    public function getIndexName(string $indexName): string
    {
        return \sprintf(
            'coreshop_index_os_%s',
            $this->slugger->slug(
                u($indexName)
                    ->trim()
                    ->lower()
                    ->toString(),
            ),
        );
    }

    public function getClient(IndexInterface $index): SearchClientInterface
    {
        $config = $index->getConfiguration();

        if (empty($config['client'])) {
            throw new \RuntimeException('Missing client in index configuration.');
        }

        $client = $this->clientRegistry->get($config['client']);

        if (!$client instanceof SearchClientInterface) {
            throw new \RuntimeException(
                \sprintf(
                    'Search client "%s" could not be found. Available clients: "%s"',
                    $config['client'],
                    \implode(', ', \array_keys($this->clientRegistry->all())),
                ),
            );
        }

        return $client;
    }

    protected function typeCastValues(IndexColumnInterface $column, $value)
    {
        return $value;
    }

    protected function handleArrayValues(IndexInterface $index, array $value): array
    {
        return $value;
    }

    protected function getSystemAttributes(): array
    {
        return [
            'o_id' => OpenSearchWorkerInterface::FIELD_TYPE_INTEGER,
            'oo_id' => OpenSearchWorkerInterface::FIELD_TYPE_INTEGER,
            'o_key' => OpenSearchWorkerInterface::FIELD_TYPE_KEYWORD,
            'o_classId' => OpenSearchWorkerInterface::FIELD_TYPE_KEYWORD,
            'o_className' => OpenSearchWorkerInterface::FIELD_TYPE_KEYWORD,
            'o_virtualObjectId' => OpenSearchWorkerInterface::FIELD_TYPE_INTEGER,
            'o_virtualObjectActive' => OpenSearchWorkerInterface::FIELD_TYPE_BOOLEAN,
            'o_type' => OpenSearchWorkerInterface::FIELD_TYPE_KEYWORD,
            'active' => OpenSearchWorkerInterface::FIELD_TYPE_BOOLEAN,
        ];
    }

    public function renderCondition(ConditionInterface $condition, array|string $params = []): mixed
    {
        $index = $params['index'] ?? null;

        Assert::isInstanceOf($index, IndexInterface::class);

        if ($params['relation'] ?? false) {
            $params['mappedFieldName'] = '_relations.' . $condition->getFieldName();
        }

        return parent::renderCondition($condition, $params);
    }

    protected function getLocalizedSystemAttributes(): array
    {
        return [
            'name' => OpenSearchWorkerInterface::FIELD_TYPE_KEYWORD,
        ];
    }

    /**
     * @return array<string, array>
     */
    private function getIndexColumns(IndexInterface $index): array
    {
        $systemColumns = $this->getSystemAttributes();
        $localizedSystemColumns = $this->getLocalizedSystemAttributes();

        foreach ($this->getExtensions($index) as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                foreach ($extension->getSystemColumns() as $name => $type) {
                    $systemColumns[$name] = $type;
                }

                foreach ($extension->getLocalizedSystemColumns() as $name => $type) {
                    $localizedSystemColumns[$name] = $type;
                }
            }
        }

        $attributes = array_map(static fn ($type) => ['type' => $type], $systemColumns);

        foreach ($localizedSystemColumns as $name => $type) {
            $attributes[$name] = $this->createLocalizedAttribute($type);
        }

        foreach ($index->getColumns() as $column) {
            $propertyName = $column->getName();
            $propertyType = $column->getColumnType();

            if ($column->getInterpreter()) {
                $interpreter = $this->interpreterRegistry->get($column->getInterpreter());

                if ($interpreter instanceof LocalizedInterpreterInterface) {
                    $attributes[$propertyName] = $this->createLocalizedAttribute($propertyType);
                } else {
                    $attributes[$propertyName] = [
                        'type' => $propertyType,
                    ];
                }
            } else {
                $attributes[$propertyName] = [
                    'type' => $propertyType,
                ];
            }
        }

        return $attributes;
    }

    /**
     * @throws \Exception
     */
    private function prepareDataForOpenSearch(IndexInterface $index, IndexableInterface $object): array
    {
        $preparedData = $this->prepareData($index, $object);
        $openSearchData = [
            ...$preparedData['data'],
            '_relations' => $preparedData['relation'],
        ];

        foreach ($preparedData['localizedData']['values'] as $language => $localizedValues) {
            foreach ($localizedValues as $name => $value) {
                $openSearchData[$name][$language] = $value;
            }
        }

        return $openSearchData;
    }

    private function createLocalizedAttribute(string $type): array
    {
        $attribute = [
            'type' => OpenSearchWorkerInterface::FIELD_TYPE_OBJECT,
            'properties' => [],
        ];

        foreach (Tool::getValidLanguages() as $locale) {
            $propertySettings = [
                'type' => $type,
            ];

            // Language analyzers are only supported for text fields
            if ($type === OpenSearchWorkerInterface::FIELD_TYPE_TEXT) {
                $analyzer = LanguageAnalyzer::fromLocale($locale);

                if ($analyzer instanceof LanguageAnalyzer) {
                    $propertySettings['analyzer'] = $analyzer->value;
                }
            }

            $attribute['properties'][$locale] = $propertySettings;
        }

        return $attribute;
    }
}
